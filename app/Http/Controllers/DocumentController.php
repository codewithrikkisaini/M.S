<?php

namespace App\Http\Controllers;

use App\Models\HotelDocument;
use App\Services\HotelDocumentService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Preview a document (stream inline).
     */
    public function preview(HotelDocument $document)
    {
        // Authorization: hotel admin can only access own documents
        if (auth()->user()->hotel_id !== $document->hotel_id) {
            abort(403, 'Unauthorized access to document.');
        }

        return HotelDocumentService::getSecureResponse($document, asDownload: false);
    }

    /**
     * Download a document (attachment).
     */
    public function download(HotelDocument $document)
    {
        // Authorization: hotel admin can only access own documents
        if (auth()->user()->hotel_id !== $document->hotel_id) {
            abort(403, 'Unauthorized access to document.');
        }

        return HotelDocumentService::getSecureResponse($document, asDownload: true);
    }
}
