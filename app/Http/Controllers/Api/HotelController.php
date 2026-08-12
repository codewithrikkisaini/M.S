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
     * Display the specified resource.
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
