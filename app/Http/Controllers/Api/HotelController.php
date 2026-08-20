<?php

namespace App\Http\Controllers\Api;

use App\Models\Hotel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hotels = Hotel::orderBy('id', 'asc')->get();

        return response()->json([
            'success' => true,
            'count' => $hotels->count(),
            'data' => $hotels
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified hotel.
     */
    public function show(string $id)
    {
        $hotel = Hotel::find($id);

        if (!$hotel) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $hotel
        ], 200);
    }

    /**
     * Get rooms belonging to a specific hotel.
     *
     * Example:
     * /api/hotels/5/rooms
     */
    public function rooms(string $id)
    {
        $hotel = Hotel::find($id);

        if (!$hotel) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel not found.'
            ], 404);
        }

        $rooms = $hotel->rooms()
            ->with('roomType')
            ->get();

        return response()->json([
            'success' => true,

            'hotel' => [
                'id' => $hotel->id,
                'hotel_code' => $hotel->hotel_code,
                'name' => $hotel->name,
                'city' => $hotel->city,
                'state' => $hotel->state,
                'country' => $hotel->country,
            ],

            'rooms_count' => $rooms->count(),

            'rooms' => $rooms
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}