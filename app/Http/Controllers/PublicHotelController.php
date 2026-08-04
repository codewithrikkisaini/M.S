<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class PublicHotelController extends Controller
{
    public function show($slug)
    {
        // 1. Load by exact slug match
        $hotel = Hotel::where('status', 'approved')
            ->where('slug', $slug)
            ->with(['images', 'rooms.roomType'])
            ->first();

        // 2. If not found, check if parameter is formatted like "buzen-suites-2030" or "grand-plaza-35"
        if (!$hotel) {
            $parts = explode('-', (string)$slug);
            $lastPart = end($parts);

            if (is_numeric($lastPart)) {
                $hotel = Hotel::where('status', 'approved')
                    ->where('id', $lastPart)
                    ->with(['images', 'rooms.roomType'])
                    ->first();
            }
        }

        // 3. Fallback for raw numeric ID (e.g. /hotel/35)
        if (!$hotel && is_numeric($slug)) {
            $hotel = Hotel::where('status', 'approved')
                ->where('id', $slug)
                ->with(['images', 'rooms.roomType'])
                ->first();
        }

        if (!$hotel) {
            abort(404, 'Hotel not found');
        }

        return view('hotel.show', compact('hotel'));
    }
}
