<?php

namespace App\Http\Controllers;

use App\Models\GuestBlacklistDocument;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BlacklistDocumentController extends Controller
{
    public function download(GuestBlacklistDocument $document): StreamedResponse
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        if ($user->hotel_id !== $document->hotel_id) {
            abort(403, 'Unauthorized access to document.');
        }

        if (!($user->hasRole('admin') || $user->hasRole('superadmin') || $user->hasRole('receptionist'))) {
            abort(403, 'You do not have permission to access blacklist documents.');
        }

        $fullPath = $document->storage_path . '/' . $document->stored_filename;

        if (!Storage::disk($document->disk)->exists($fullPath)) {
            abort(404, 'Document not found.');
        }

        return Storage::disk($document->disk)->download(
            $fullPath,
            $document->original_filename
        );
    }
}
