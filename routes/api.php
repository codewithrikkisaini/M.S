<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HotelController;

Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/hotels/{id}', [HotelController::class, 'show']);