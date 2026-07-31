<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class PublicHotelController extends Controller
{
    public function show($id)
    {
        // Load the hotel along with its active rooms and images if approved
        $hotel = Hotel::where('status', 'approved')
            ->with(['images', 'rooms.roomType'])->findOrFail($id);

        return view('hotel.show', compact('hotel'));
    }
}
