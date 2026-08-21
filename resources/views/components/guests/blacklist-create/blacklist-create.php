<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Guest;
use App\Models\GuestBlacklist;
use App\Models\GuestBlacklistDocument;
use App\Services\GuestBlacklistService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

new class extends Component
{
    use WithFileUploads;

    public string $search_guest = '';
    public ?int $selected_guest_id = null;
    public ?Guest $selected_guest = null;

    public string $first_name = '';
    public string $last_name = '';
    public string $id_type = '';
    public string $id_number = '';
    public string $date_of_birth = '';
    public string $reason = '';

    public $documents = [];
    public array $existing_documents = [];
    public array $document_categories = [];

    public bool $show_guest_search = true;
    public bool $show_confirm_modal = false;

    public function mount(): void
    {
        if (!auth()->check() || (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('superadmin'))) {
            $this->dispatch('toast', message: 'You do not have permission to create blacklist entries.', type: 'error');
            $this->redirect(route('guests.blacklist.index'), navigate: true);
        }
    }

    public function updatedSearchGuest(): void
    {
        $this->selected_guest_id = null;
        $this->selected_guest = null;
    }

    public function selectGuest(int $guestId): void
    {
        $guest = Guest::findOrFail($guestId);
        $this->selected_guest_id = $guestId;
        $this->selected_guest = $guest;

        $nameParts = explode(' ', trim($guest->name), 2);
        $this->first_name = $nameParts[0] ?? '';
        $this->last_name = $nameParts[1] ?? '';
        $this->id_type = $guest->id_type ?? '';
        $this->id_number = $guest->id_number ?? $guest->passport_number ?? '';
        $this->show_guest_search = false;
    }

    public function clearGuest(): void
    {
        $this->selected_guest_id = null;
        $this->selected_guest = null;
        $this->first_name = '';
        $this->last_name = '';
        $this->id_type = '';
        $this->id_number = '';
        $this->date_of_birth = '';
        $this->reason = '';
        $this->documents = [];
        $this->document_categories = [];
        $this->show_guest_search = true;
    }

    public function openConfirmModal(): void
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

        $this->show_confirm_modal = true;
    }

    public function closeConfirmModal(): void
    {
        $this->show_confirm_modal = false;
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

        if ($this->selected_guest_id && $service->hasActiveBlacklist($this->selected_guest_id)) {
            $this->addError('first_name', 'This guest is already blacklisted.');
            return;
        }

        if (!$this->selected_guest_id && $service->hasActiveBlacklist(
            null,
            $this->id_number ?: null,
            $this->first_name,
            $this->last_name
        )) {
            $this->addError('first_name', 'A guest with this identity is already blacklisted.');
            return;
        }

        $blacklist = GuestBlacklist::create([
            'hotel_id'       => auth()->user()->hotel_id,
            'guest_id'       => $this->selected_guest_id,
            'first_name'     => trim($this->first_name),
            'last_name'      => trim($this->last_name),
            'id_type'        => $this->id_type ?: null,
            'id_number'      => $this->id_number ?: null,
            'date_of_birth'  => $this->date_of_birth ?: null,
            'reason'         => $this->reason,
            'status'         => 'active',
            'blacklisted_by' => auth()->id(),
        ]);

        // Handle document uploads
        foreach ($this->documents as $file) {
            if ($file) {
                $originalName = $file->getClientOriginalName();
                $storedName = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('blacklist-documents', $storedName, 'local');

                GuestBlacklistDocument::create([
                    'guest_blacklist_id' => $blacklist->id,
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

        $this->show_confirm_modal = false;

        \App\Models\ActivityLog::log(
            'Guest Blacklisted',
            "Guest {$this->first_name} {$this->last_name} has been blacklisted. Reason: {$this->reason}"
        );

        session()->flash('toast', ['message' => 'Guest has been blacklisted successfully.', 'type' => 'success']);
        $this->redirect(route('guests.blacklist.index'), navigate: true);
    }

    public function searchResults(): \Illuminate\Support\Collection
    {
        if (strlen($this->search_guest) < 2) {
            return collect();
        }

        return Guest::where('name', 'like', "%{$this->search_guest}%")
            ->orWhere('email', 'like', "%{$this->search_guest}%")
            ->orWhere('id_number', 'like', "%{$this->search_guest}%")
            ->orWhere('passport_number', 'like', "%{$this->search_guest}%")
            ->limit(10)
            ->get();
    }

    public function render(): mixed
    {
        $guests = $this->searchResults();
        return $this->view(compact('guests'));
    }
};
