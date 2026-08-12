<?php

namespace App\Http\Controllers;

use App\Models\HotelDocument;
use App\Services\HotelDocumentService;
use Illuminate\Http\Request;

class SuperAdminDocumentController extends Controller
{
    /**
     * Preview any hotel document (super admin).
     */
    public function preview(HotelDocument $document)
    {
        return HotelDocumentService::getSecureResponse($document, asDownload: false);
    }

    /**
     * Download any hotel document (super admin).
     */
    public function download(HotelDocument $document)
    {
        return HotelDocumentService::getSecureResponse($document, asDownload: true);
    }
}
