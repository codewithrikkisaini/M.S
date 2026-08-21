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

    public bool $showReleaseModal = false;
    public string $release_reason = '';
    public string $release_notes = '';
    public $release_documents = [];

    public bool $showReBlacklistModal = false;
    public string $reBlacklist_reason = '';

    public bool $showDocumentModal = false;
    public ?GuestBlacklistDocument $viewingDocument = null;

    public function mount(GuestBlacklist $blacklist): void
    {
        $this->blacklist = $blacklist->load(['guest', 'blacklister', 'releaser', 'documents.uploader']);
    }

    public function openReleaseModal(): void
    {
        $this->resetErrorBag();
        $this->release_reason = '';
        $this->release_notes = '';
        $this->release_documents = [];
        $this->showReleaseModal = true;
    }

    public function closeReleaseModal(): void
    {
        $this->showReleaseModal = false;
        $this->release_reason = '';
        $this->release_notes = '';
        $this->release_documents = [];
        $this->resetErrorBag();
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

    public function openReBlacklistModal(): void
    {
        $this->resetErrorBag();
        $this->reBlacklist_reason = '';
        $this->showReBlacklistModal = true;
    }

    public function closeReBlacklistModal(): void
    {
        $this->showReBlacklistModal = false;
        $this->reBlacklist_reason = '';
        $this->resetErrorBag();
    }

    public function reBlacklist(GuestBlacklistService $service): void
    {
        $this->validate([
            'reBlacklist_reason' => 'required|string|max:2000',
        ]);

        $newCase = $service->reBlacklist($this->blacklist->id);
        $newCase->update(['reason' => $this->reBlacklist_reason]);

        $this->dispatch('toast', message: 'Guest has been re-blacklisted. New case: ' . $newCase->case_number, type: 'success');
        $this->redirect(route('guests.blacklist.show', $newCase->id), navigate: true);
    }

    public function viewDocument(int $documentId): void
    {
        $doc = GuestBlacklistDocument::find($documentId);
        if (!$doc || $doc->guest_blacklist_id !== $this->blacklist->id) {
            $this->dispatch('toast', message: 'Document not found.', type: 'error');
            return;
        }
        $this->viewingDocument = $doc;
        $this->showDocumentModal = true;
    }

    public function closeDocumentModal(): void
    {
        $this->showDocumentModal = false;
        $this->viewingDocument = null;
    }

    public function deleteDocument(int $documentId): void
    {
        $doc = GuestBlacklistDocument::find($documentId);
        if (!$doc || $doc->guest_blacklist_id !== $this->blacklist->id) {
            $this->dispatch('toast', message: 'Document not found.', type: 'error');
            return;
        }

        $fullPath = $doc->getFullStoragePath();
        if (Storage::disk($doc->disk)->exists($fullPath)) {
            Storage::disk($doc->disk)->delete($fullPath);
        }

        $doc->delete();
        $this->blacklist = $this->blacklist->fresh()->load(['guest', 'blacklister', 'releaser', 'documents.uploader']);
        $this->dispatch('toast', message: 'Document deleted.', type: 'success');
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
