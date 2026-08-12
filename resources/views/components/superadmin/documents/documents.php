<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Hotel;
use App\Models\HotelDocument;
use App\Models\DocumentAuditLog;
use App\Services\HotelDocumentService;
use App\Services\NotificationService;
use App\Enums\DocumentStatus;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $filterStatus = '';
    public $filterDocumentType = '';
    public $filterHotel = '';
    public $sortBy = 'latest';
    public $showPendingOnly = false;

    // Review modal
    public $showReviewModal = false;
    public $reviewDocument = null;
    public $reviewHotel = null;
    public $reviewAction = '';
    public $reviewReason = '';
    public $reviewComment = '';

    // View modal
    public $showViewModal = false;
    public $viewingDocument = null;

    // History modal
    public $showHistoryModal = false;
    public $historyHotelId = null;
    public $historyDocs = [];
    public $historyTypeName = '';

    // Stats
    public $pendingCount = 0;
    public $totalDocs = 0;

    public function mount(): void
    {
        $this->loadStats();
    }

    public function loadStats(): void
    {
        $this->pendingCount = HotelDocument::where('status', DocumentStatus::Pending)
            ->orWhere('status', DocumentStatus::UnderReview)
            ->count();
        $this->totalDocs = HotelDocument::where('is_current', true)->count();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDocumentType(): void
    {
        $this->resetPage();
    }

    public function updatingShowPendingOnly(): void
    {
        $this->resetPage();
    }

    public function getDocuments()
    {
        $query = HotelDocument::with(['hotel', 'uploader', 'reviewer'])
            ->where('is_current', true);

        // Search
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('hotel', function ($hq) use ($search) {
                    $hq->where('name', 'like', "%{$search}%")
                       ->orWhere('hotel_code', 'like', "%{$search}%")
                       ->orWhere('owner_name', 'like', "%{$search}%");
                })->orWhere('document_name', 'like', "%{$search}%")
                  ->orWhere('original_filename', 'like', "%{$search}%")
                  ->orWhere('document_type', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // Document type filter
        if ($this->filterDocumentType) {
            $query->where('document_type', $this->filterDocumentType);
        }

        // Pending only
        if ($this->showPendingOnly) {
            $query->whereIn('status', [DocumentStatus::Pending, DocumentStatus::UnderReview]);
        }

        // Sorting
        switch ($this->sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'pending_first':
                $query->orderByRaw("FIELD(status, 'pending', 'under_review', 'replacement_required', 'rejected', 'approved')")
                      ->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        return $query->paginate(15);
    }

    public function openReview(int $documentId, string $action): void
    {
        $doc = HotelDocument::with('hotel')->findOrFail($documentId);
        $this->reviewDocument = $doc;
        $this->reviewHotel = $doc->hotel;
        $this->reviewAction = $action;
        $this->reviewReason = '';
        $this->reviewComment = '';
        $this->showReviewModal = true;
    }

    public function closeReview(): void
    {
        $this->showReviewModal = false;
        $this->reviewDocument = null;
        $this->reviewHotel = null;
        $this->reviewAction = '';
        $this->reviewReason = '';
        $this->reviewComment = '';
    }

    public function submitReview(): void
    {
        if (!$this->reviewDocument) return;

        $docId = $this->reviewDocument->id;
        $hotelId = $this->reviewDocument->hotel_id;

        try {
            match ($this->reviewAction) {
                'approve' => HotelDocumentService::approveDocument($docId, $this->reviewComment ?: null),
                'reject' => HotelDocumentService::rejectDocument($docId, $this->reviewReason, $this->reviewComment ?: null),
                'request_replacement' => HotelDocumentService::requestReplacement($docId, $this->reviewReason, $this->reviewComment ?: null),
            };

            // Send notification to hotel admin
            $hotel = $this->reviewHotel;
            $docName = $this->reviewDocument->document_name;

            match ($this->reviewAction) {
                'approve' => NotificationService::notifyHotel(
                    $hotelId,
                    'Document Approved',
                    "Your document \"{$docName}\" has been approved by the administration.",
                    '/hotel-documents'
                ),
                'reject' => NotificationService::notifyHotel(
                    $hotelId,
                    'Document Rejected',
                    "Your document \"{$docName}\" has been rejected. Reason: {$this->reviewReason}",
                    '/hotel-documents'
                ),
                'request_replacement' => NotificationService::notifyHotel(
                    $hotelId,
                    'Document Update Required',
                    "Please update your document \"{$docName}\". Reason: {$this->reviewReason}",
                    '/hotel-documents'
                ),
            };

            $this->closeReview();
            $this->loadStats();

            $this->dispatch('toast', [
                'type' => match($this->reviewAction) {
                    'approve' => 'success',
                    'reject' => 'warning',
                    'request_replacement' => 'info',
                    default => 'success',
                },
                'message' => 'Document review submitted successfully.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Review failed: ' . $e->getMessage(),
            ]);
        }
    }

    public function viewDocument(int $documentId): void
    {
        $this->viewingDocument = HotelDocument::with(['hotel', 'uploader', 'reviewer'])->find($documentId);
        $this->showViewModal = true;
    }

    public function closeView(): void
    {
        $this->showViewModal = false;
        $this->viewingDocument = null;
    }

    public function viewHistory(int $hotelId, string $documentType, string $typeName): void
    {
        $this->historyHotelId = $hotelId;
        $this->historyTypeName = $typeName;
        $this->historyDocs = HotelDocument::where('hotel_id', $hotelId)
            ->where('document_type', $documentType)
            ->with(['uploader', 'reviewer'])
            ->orderByDesc('version')
            ->get()
            ->toArray();
        $this->showHistoryModal = true;
    }

    public function closeHistory(): void
    {
        $this->showHistoryModal = false;
        $this->historyDocs = [];
        $this->historyTypeName = '';
    }

    public function render(): mixed
    {
        return $this->view([
            'documents' => $this->getDocuments(),
            'allTypes' => HotelDocumentService::getAllTypes(),
        ]);
    }
};
