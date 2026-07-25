<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class PublicHotelController extends Controller
{
    public function show($id)
    {
        // Load the hotel along with its active rooms and images
        $hotel = Hotel::with(['images', 'rooms' => function($q) {
            $q->where('is_active', true);
        }])->findOrFail($id);

        return view('hotel.show', compact('hotel'));
    }
}
