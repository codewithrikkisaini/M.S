<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HotelController;

Route::get('/test', function () {
    return response()->json(['message' => 'API Working']);
});

Route::apiResource('hotel', HotelController::class);
