<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\GuestBlacklist;
use App\Models\GuestBlacklistDocument;
use App\Services\GuestBlacklistService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

new class extends Component
{
    use WithFileUploads;

    public GuestBlacklist $blacklist;
    public string $first_name = '';
    public string $last_name = '';
    public string $id_type = '';
    public string $id_number = '';
    public string $date_of_birth = '';
    public string $reason = '';

    public $documents = [];
    public array $existing_documents = [];

    public bool $showReleaseModal = false;
    public string $release_reason = '';
    public string $release_notes = '';
    public $release_documents = [];

    public function mount(GuestBlacklist $blacklist): void
    {
        if (!auth()->check() || (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('superadmin'))) {
            $this->dispatch('toast', message: 'You do not have permission to edit blacklist entries.', type: 'error');
            $this->redirect(route('guests.blacklist.index'), navigate: true);
            return;
        }

        $this->blacklist = $blacklist->load(['documents']);
        $this->first_name = $blacklist->first_name;
        $this->last_name = $blacklist->last_name;
        $this->id_type = $blacklist->id_type ?? '';
        $this->id_number = $blacklist->id_number ?? '';
        $this->date_of_birth = $blacklist->date_of_birth ? $blacklist->date_of_birth->format('Y-m-d') : '';
        $this->reason = $blacklist->reason;
        $this->existing_documents = $blacklist->documents->map(function ($doc) {
            return $this->formatDocument($doc);
        })->toArray();
    }

    public function deleteDocument(int $documentId): void
    {
        $doc = GuestBlacklistDocument::findOrFail($documentId);

        if ($doc->guest_blacklist_id !== $this->blacklist->id) {
            $this->dispatch('toast', message: 'Unauthorized access to document.', type: 'error');
            return;
        }

        $fullPath = $doc->getFullStoragePath();
        if (Storage::disk($doc->disk)->exists($fullPath)) {
            Storage::disk($doc->disk)->delete($fullPath);
        }

        $doc->delete();

        $this->refreshDocuments();
        $this->dispatch('toast', message: 'Document deleted.', type: 'success');
    }

    public function save(GuestBlacklistService $service): void
    {
        $this->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'id_type'      => 'nullable|string|max:100',
            'id_number'    => 'nullable|string|max:100',
            'date_of_birth'=> 'nullable|date|before:today',
            'reason'       => 'required|string|max:2000',
            'documents.*'  => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        $this->blacklist->update([
            'first_name'     => trim($this->first_name),
            'last_name'      => trim($this->last_name),
            'id_type'        => $this->id_type ?: null,
            'id_number'      => $this->id_number ?: null,
            'date_of_birth'  => $this->date_of_birth ?: null,
            'reason'         => $this->reason,
        ]);

        foreach ($this->documents as $file) {
            if ($file) {
                $originalName = $file->getClientOriginalName();
                $storedName = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('blacklist-documents', $storedName, 'local');

                GuestBlacklistDocument::create([
                    'guest_blacklist_id' => $this->blacklist->id,
                    'hotel_id'           => auth()->user()->hotel_id,
                    'original_filename'  => $originalName,
                    'stored_filename'    => $storedName,
                    'disk'               => 'local',
                    'storage_path'       => 'blacklist-documents',
                    'mime_type'          => $file->getMimeType(),
                    'file_size'          => $file->getSize(),
                    'uploaded_by'        => auth()->id(),
                ]);
            }
        }

        $this->refreshDocuments();

        \App\Models\ActivityLog::log(
            'Blacklist Updated',
            "Blacklist record for {$this->first_name} {$this->last_name} has been updated."
        );

        $this->dispatch('toast', message: 'Blacklist updated successfully.', type: 'success');
        $this->redirect(route('guests.blacklist.edit', $this->blacklist->id), navigate: true);
    }

    public function openReleaseModal(): void
    {
        $this->showReleaseModal = true;
        $this->release_reason = '';
        $this->release_notes = '';
        $this->release_documents = [];
    }

    public function closeReleaseModal(): void
    {
        $this->showReleaseModal = false;
        $this->release_reason = '';
        $this->release_notes = '';
        $this->release_documents = [];
        $this->resetValidation();
    }

    public function releaseBlacklist(GuestBlacklistService $service): void
    {
        $this->validate([
            'release_reason' => 'required|string|max:2000',
            'release_notes' => 'nullable|string|max:2000',
            'release_documents.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        $service->releaseBlacklist(
            $this->blacklist->id,
            $this->release_reason,
            $this->release_notes,
            $this->release_documents
        );

        $this->dispatch('toast', message: 'Guest has been released from blacklist.', type: 'success');
        $this->redirect(route('guests.blacklist.show', $this->blacklist->id), navigate: true);
    }

    public function removeNewDocument(int $index): void
    {
        unset($this->documents[$index]);
        $this->documents = array_values($this->documents);
    }

    private function refreshDocuments(): void
    {
        $this->existing_documents = $this->blacklist->fresh()->documents->map(function ($doc) {
            return $this->formatDocument($doc);
        })->toArray();
    }

    private function formatDocument($doc): array
    {
        return [
            'id' => $doc->id,
            'name' => $doc->original_filename,
            'original_filename' => $doc->original_filename,
            'mime_type' => $doc->mime_type,
            'file_size' => $doc->file_size_formatted,
            'category' => $doc->category ?? 'Other',
            'uploaded_at' => $doc->created_at->format('d M Y'),
            'view_url' => route('guests.blacklist.document.preview', $doc->id),
            'download_url' => route('guests.blacklist.document.download', $doc->id),
        ];
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
