<?php

namespace App\Services;

use App\Models\HotelDocument;
use App\Models\DocumentAuditLog;
use App\Models\Hotel;
use App\Enums\DocumentStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HotelDocumentService
{
    /**
     * Get the required document types for a hotel, considering country-specific rules.
     */
    public static function getRequiredTypes(?string $country = null): array
    {
        $config = config('hotel_documents.types', []);
        $countryReqs = config('hotel_documents.country_requirements', []);

        $requiredKeys = [];
        if ($country && isset($countryReqs[strtolower($country)])) {
            $requiredKeys = $countryReqs[strtolower($country)];
        } else {
            foreach ($config as $key => $type) {
                if ($type['required']) {
                    $requiredKeys[] = $key;
                }
            }
        }

        $result = [];
        foreach ($requiredKeys as $key) {
            if (isset($config[$key])) {
                $result[$key] = $config[$key];
            }
        }

        return $result;
    }

    /**
     * Get all document type definitions.
     */
    public static function getAllTypes(): array
    {
        return config('hotel_documents.types', []);
    }

    /**
     * Get a single document type definition by key.
     */
    public static function getType(string $key): ?array
    {
        return config("hotel_documents.types.{$key}");
    }

    /**
     * Get the current document for a given type and hotel.
     */
    public static function getCurrentDocument(int $hotelId, string $documentType): ?HotelDocument
    {
        return HotelDocument::where('hotel_id', $hotelId)
            ->where('document_type', $documentType)
            ->where('is_current', true)
            ->latest('version')
            ->first();
    }

    /**
     * Get all versions (history) for a document type and hotel.
     */
    public static function getDocumentHistory(int $hotelId, string $documentType): \Illuminate\Database\Eloquent\Collection
    {
        return HotelDocument::where('hotel_id', $hotelId)
            ->where('document_type', $documentType)
            ->orderByDesc('version')
            ->get();
    }

    /**
     * Upload a new document.
     */
    public static function uploadDocument(
        int $hotelId,
        string $documentType,
        UploadedFile $file,
        ?string $description = null
    ): HotelDocument {
        $typeConfig = self::getType($documentType);
        if (!$typeConfig) {
            throw new \InvalidArgumentException("Unknown document type: {$documentType}");
        }

        // Determine next version
        $maxVersion = HotelDocument::where('hotel_id', $hotelId)
            ->where('document_type', $documentType)
            ->max('version') ?? 0;

        // If there's a current document that is approved, don't allow overwrite (create new version)
        $currentDoc = self::getCurrentDocument($hotelId, $documentType);

        // Mark old current docs as non-current
        HotelDocument::where('hotel_id', $hotelId)
            ->where('document_type', $documentType)
            ->where('is_current', true)
            ->update(['is_current' => false]);

        // Store file privately
        $storedFilename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $storagePath = "hotel-documents/{$hotelId}/{$documentType}";
        $file->storeAs($storagePath, $storedFilename, 'local');

        $doc = HotelDocument::create([
            'hotel_id'         => $hotelId,
            'document_type'    => $documentType,
            'document_name'    => $typeConfig['name'],
            'description'      => $description,
            'original_filename'=> $file->getClientOriginalName(),
            'stored_filename'  => $storedFilename,
            'disk'             => 'local',
            'storage_path'     => $storagePath,
            'mime_type'        => $file->getMimeType(),
            'file_size'        => $file->getSize(),
            'status'           => DocumentStatus::Pending,
            'version'          => $maxVersion + 1,
            'is_current'       => true,
            'uploaded_by'      => auth()->id(),
            'uploaded_at'      => now(),
        ]);

        // Audit log
        DocumentAuditLog::log(
            $doc,
            'uploaded',
            $currentDoc?->status?->value,
            DocumentStatus::Pending->value,
            'Document v' . $doc->version . ' uploaded'
        );

        return $doc;
    }

    /**
     * Replace a rejected/required document with a new version.
     */
    public static function replaceDocument(
        int $documentId,
        UploadedFile $file,
        ?string $description = null
    ): HotelDocument {
        $oldDoc = HotelDocument::findOrFail($documentId);

        if (!$oldDoc->status->canBeReplaced()) {
            throw new \LogicException('This document cannot be replaced in its current status.');
        }

        $hotelId = $oldDoc->hotel_id;
        $documentType = $oldDoc->document_type;

        // Mark old doc as non-current
        $oldDoc->update(['is_current' => false]);

        // Store new file
        $storedFilename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $storagePath = "hotel-documents/{$hotelId}/{$documentType}";
        $file->storeAs($storagePath, $storedFilename, 'local');

        $doc = HotelDocument::create([
            'hotel_id'         => $hotelId,
            'document_type'    => $documentType,
            'document_name'    => $oldDoc->document_name,
            'description'      => $description ?? $oldDoc->description,
            'original_filename'=> $file->getClientOriginalName(),
            'stored_filename'  => $storedFilename,
            'disk'             => 'local',
            'storage_path'     => $storagePath,
            'mime_type'        => $file->getMimeType(),
            'file_size'        => $file->getSize(),
            'status'           => DocumentStatus::Pending,
            'version'          => $oldDoc->version + 1,
            'is_current'       => true,
            'uploaded_by'      => auth()->id(),
            'uploaded_at'      => now(),
        ]);

        DocumentAuditLog::log(
            $doc,
            'replaced',
            $oldDoc->status->value,
            DocumentStatus::Pending->value,
            'Document replaced from v' . $oldDoc->version . ' to v' . $doc->version
        );

        return $doc;
    }

    /**
     * Approve a document.
     */
    public static function approveDocument(int $documentId, ?string $comment = null): HotelDocument
    {
        $doc = HotelDocument::findOrFail($documentId);
        $oldStatus = $doc->status->value;

        $doc->update([
            'status'       => DocumentStatus::Approved,
            'reviewed_by'  => auth()->id(),
            'reviewed_at'  => now(),
            'admin_comment'=> $comment,
        ]);

        DocumentAuditLog::log(
            $doc,
            'approved',
            $oldStatus,
            DocumentStatus::Approved->value,
            $comment
        );

        return $doc;
    }

    /**
     * Reject a document with a reason.
     */
    public static function rejectDocument(int $documentId, string $reason, ?string $comment = null): HotelDocument
    {
        if (empty($reason)) {
            throw new \InvalidArgumentException('Rejection reason is required.');
        }

        $doc = HotelDocument::findOrFail($documentId);
        $oldStatus = $doc->status->value;

        $doc->update([
            'status'           => DocumentStatus::Rejected,
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
            'rejection_reason' => $reason,
            'admin_comment'    => $comment,
        ]);

        DocumentAuditLog::log(
            $doc,
            'rejected',
            $oldStatus,
            DocumentStatus::Rejected->value,
            $reason
        );

        return $doc;
    }

    /**
     * Request replacement / more information for a document.
     */
    public static function requestReplacement(int $documentId, string $reason, ?string $comment = null): HotelDocument
    {
        $doc = HotelDocument::findOrFail($documentId);
        $oldStatus = $doc->status->value;

        $doc->update([
            'status'           => DocumentStatus::ReplacementRequired,
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
            'rejection_reason' => $reason,
            'admin_comment'    => $comment,
        ]);

        DocumentAuditLog::log(
            $doc,
            'replacement_requested',
            $oldStatus,
            DocumentStatus::ReplacementRequired->value,
            $reason
        );

        return $doc;
    }

    /**
     * Securely stream a document for preview/download.
     */
    public static function getSecureResponse(HotelDocument $doc, bool $asDownload = false)
    {
        $path = $doc->getFullStoragePath();

        if (!Storage::disk($doc->disk)->exists($path)) {
            abort(404, 'Document file not found.');
        }

        $headers = [
            'Content-Type' => $doc->mime_type,
            'Content-Disposition' => $asDownload
                ? 'attachment; filename="' . $doc->original_filename . '"'
                : 'inline; filename="' . $doc->original_filename . '"',
        ];

        return Storage::disk($doc->disk)->download($path, $doc->original_filename, $headers);
    }

    /**
     * Check if all required documents are uploaded for a hotel.
     */
    public static function allRequiredUploaded(int $hotelId, ?string $country = null): bool
    {
        $required = self::getRequiredTypes($country);

        foreach ($required as $key => $type) {
            $current = self::getCurrentDocument($hotelId, $key);
            if (!$current) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get document completeness status for a hotel.
     */
    public static function getCompletenessStatus(int $hotelId, ?string $country = null): array
    {
        $required = self::getRequiredTypes($country);
        $allTypes = self::getAllTypes();

        $result = [];
        foreach ($allTypes as $key => $type) {
            $current = self::getCurrentDocument($hotelId, $key);
            $isRequired = isset($required[$key]);

            $result[$key] = [
                'type'        => $type,
                'required'    => $isRequired,
                'document'    => $current,
                'uploaded'    => $current !== null,
                'status'      => $current?->status?->value,
                'is_approved' => $current && $current->status === DocumentStatus::Approved,
            ];
        }

        return $result;
    }
}
