<?php

namespace App\Http\Controllers;

use App\Models\GuestBlacklistDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;

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

    public function preview(GuestBlacklistDocument $document): Response
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

        $fileContent = Storage::disk($document->disk)->get($fullPath);
        $mimeType = $document->mime_type ?: 'application/octet-stream';

        return response($fileContent, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $document->original_filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
}
