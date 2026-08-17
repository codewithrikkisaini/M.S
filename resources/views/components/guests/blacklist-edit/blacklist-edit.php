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

    public function mount(GuestBlacklist $blacklist): void
    {
        if (!auth()->check() || (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('superadmin'))) {
            session()->flash('toast', ['message' => 'You do not have permission to edit blacklist entries.', 'type' => 'error']);
            $this->redirect(route('guests.blacklist.index'), navigate: true);
            return;
        }

        $this->blacklist = $blacklist;
        $this->first_name = $blacklist->first_name;
        $this->last_name = $blacklist->last_name;
        $this->id_type = $blacklist->id_type ?? '';
        $this->id_number = $blacklist->id_number ?? '';
        $this->date_of_birth = $blacklist->date_of_birth ? $blacklist->date_of_birth->format('Y-m-d') : '';
        $this->reason = $blacklist->reason;
        $this->existing_documents = $blacklist->documents->toArray();
    }

    public function deleteDocument(int $documentId): void
    {
        $doc = GuestBlacklistDocument::findOrFail($documentId);
        
        if ($doc->guest_blacklist_id !== $this->blacklist->id) {
            abort(403);
        }

        $fullPath = $doc->getFullStoragePath();
        if (Storage::disk($doc->disk)->exists($fullPath)) {
            Storage::disk($doc->disk)->delete($fullPath);
        }

        $doc->delete();

        $this->existing_documents = $this->blacklist->fresh()->documents->toArray();
        $this->dispatch('toast', message: 'Document deleted.', type: 'success');
    }

    public function save(GuestBlacklistService $service): void
    {
        $this->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'id_type'      => 'nullable|in:Passport,Driver\'s License,Aadhaar Card,Voter ID,Other',
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

        // Handle new document uploads
        foreach ($this->documents as $file) {
            if ($file) {
                $originalName = $file->getClientOriginalName();
                $storedName = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('blacklist-documents', $storedName, 'local');

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

        $this->existing_documents = $this->blacklist->fresh()->documents->toArray();

        \App\Models\ActivityLog::log(
            'Blacklist Updated',
            "Blacklist record for {$this->first_name} {$this->last_name} has been updated."
        );

        session()->flash('toast', ['message' => 'Blacklist updated successfully.', 'type' => 'success']);
        $this->redirect(route('guests.blacklist.index'), navigate: true);
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
