<?php

use Livewire\Component;
use App\Models\HotelDocument;
use App\Services\HotelDocumentService;
use App\Services\NotificationService;
use App\Enums\DocumentStatus;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    use WithFileUploads;

    public $documents;
    public $completeness = [];
    public $totalFiles = 0;
    public $requiredCount = 0;
    public $uploadedCount = 0;
    public $approvedCount = 0;
    public $verificationScore = 0;
    public $verificationStatus = '';

    public $uploadFile = null;
    public $uploadType = '';
    public $uploadName = '';
    public $isUploading = false;

    public $search = '';

    public $replacingDocumentId = null;
    public $replaceFile = null;

    public $showViewModal = false;
    public $viewingDocument = null;

    public $showHistoryModal = false;
    public $historyType = '';
    public $historyDocs = [];

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $hotelId = Auth::user()->hotel_id;

        if (!$hotelId) {
            $this->documents = collect();
            $this->completeness = [];
            $this->totalFiles = 0;
            $this->requiredCount = 0;
            $this->uploadedCount = 0;
            $this->approvedCount = 0;
            $this->verificationScore = 0;
            $this->verificationStatus = 'Documents Required';
            return;
        }

        $hotel = Auth::user()->hotel;
        $country = $hotel?->country;

        $this->completeness = HotelDocumentService::getCompletenessStatus($hotelId, $country);

        $this->documents = HotelDocument::where('hotel_id', $hotelId)
            ->where('is_current', true)
            ->with(['uploader', 'reviewer'])
            ->orderBy('document_type')
            ->get()
            ->keyBy('document_type');

        $this->totalFiles = $this->documents->count();

        $requiredItems = array_filter($this->completeness, fn($c) => $c['required']);
        $this->requiredCount = count($requiredItems);
        $this->uploadedCount = count(array_filter($this->completeness, fn($c) => $c['uploaded']));
        $this->approvedCount = count(array_filter($this->completeness, fn($c) => $c['is_approved']));

        $this->verificationScore = $this->requiredCount > 0
            ? round(($this->approvedCount / $this->requiredCount) * 100)
            : 0;

        $pendingDocs = $this->documents->filter(fn($d) => in_array($d->status, [
            DocumentStatus::Pending, DocumentStatus::UnderReview,
        ]));
        $rejectedDocs = $this->documents->filter(fn($d) => in_array($d->status, [
            DocumentStatus::Rejected, DocumentStatus::ReplacementRequired,
        ]));

        if ($this->requiredCount > 0 && $this->approvedCount >= $this->requiredCount) {
            $this->verificationStatus = 'Verified';
        } elseif ($rejectedDocs->isNotEmpty()) {
            $this->verificationStatus = 'Action Required';
        } elseif ($pendingDocs->isNotEmpty()) {
            $this->verificationStatus = 'Pending Review';
        } elseif ($this->uploadedCount > 0) {
            $this->verificationStatus = 'Partially Verified';
        } else {
            $this->verificationStatus = 'Documents Required';
        }
    }

    public function getFilteredDocuments()
    {
        if (!$this->search) return $this->documents;

        $search = strtolower($this->search);
        return $this->documents->filter(function ($doc) use ($search) {
            return str_contains(strtolower($doc->document_name), $search)
                || str_contains(strtolower($doc->document_type), $search)
                || str_contains(strtolower($doc->original_filename), $search);
        });
    }

    public function submitUpload(): void
    {
        $hotelId = Auth::user()->hotel_id;

        if (!$hotelId) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'No hotel associated with your account.']);
            return;
        }

        $this->validate([
            'uploadFile' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'uploadType' => 'required|string',
            'uploadName' => 'required|string|max:255',
        ]);

        $this->isUploading = true;

        try {
            $typeConfig = HotelDocumentService::getType($this->uploadType);
            if (!$typeConfig) {
                throw new \InvalidArgumentException('Invalid document type.');
            }

            $doc = HotelDocumentService::uploadDocument(
                $hotelId,
                $this->uploadType,
                $this->uploadFile,
                $this->uploadName
            );

            NotificationService::notifySuperAdmins(
                'New Hotel Document',
                Auth::user()->hotel->name . ' uploaded: ' . $this->uploadName,
                '/superadmin/documents',
                'document_uploaded'
            );

            $this->uploadFile = null;
            $this->uploadType = '';
            $this->uploadName = '';
            $this->isUploading = false;

            $this->loadData();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Document uploaded successfully. It is now pending Super Admin review.',
            ]);
        } catch (\Exception $e) {
            $this->isUploading = false;
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Upload failed: ' . $e->getMessage(),
            ]);
        }
    }

    public function startReplace(int $documentId): void
    {
        $doc = HotelDocument::findOrFail($documentId);

        if ($doc->hotel_id !== Auth::user()->hotel_id) {
            abort(403);
        }

        if (!$doc->status->canBeReplaced()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'This document cannot be replaced in its current status.',
            ]);
            return;
        }

        $this->replacingDocumentId = $documentId;
        $this->replaceFile = null;
    }

    public function cancelReplace(): void
    {
        $this->replacingDocumentId = null;
        $this->replaceFile = null;
    }

    public function submitReplace(): void
    {
        $this->validate([
            'replaceFile' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);

        try {
            $doc = HotelDocumentService::replaceDocument(
                $this->replacingDocumentId,
                $this->replaceFile,
                null
            );

            NotificationService::notifySuperAdmins(
                'Document Replacement',
                Auth::user()->hotel->name . ' uploaded a replacement: ' . $doc->document_name,
                '/superadmin/documents',
                'document_replaced'
            );

            $this->cancelReplace();
            $this->loadData();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Document replaced successfully. New version submitted for review.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Replace failed: ' . $e->getMessage(),
            ]);
        }
    }

    public function viewDocument(string $type): void
    {
        $this->viewingDocument = $this->documents->get($type);
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingDocument = null;
    }

    public function viewHistory(string $type): void
    {
        $hotelId = Auth::user()->hotel_id;

        if (!$hotelId) return;

        $this->historyType = $type;
        $this->historyDocs = HotelDocumentService::getDocumentHistory($hotelId, $type)->toArray();
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal(): void
    {
        $this->showHistoryModal = false;
        $this->historyType = '';
        $this->historyDocs = [];
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
