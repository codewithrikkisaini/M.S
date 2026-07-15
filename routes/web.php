<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ─── Public ────────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));
Route::get('/login',  [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// ─── Auth-protected (all MFC via Route::livewire) ──────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::livewire('/dashboard', 'dashboard')->name('dashboard');

    // Admin-only Routes
    Route::middleware('admin')->group(function () {
        // Rooms
        Route::livewire('/rooms', 'rooms.room-list')->name('rooms.index');
        Route::livewire('/rooms/create', 'rooms.room-create')->name('rooms.create');
        Route::livewire('/rooms/types', 'rooms.room-types')->name('rooms.types');
        Route::livewire('/rooms/{room}/edit', 'rooms.room-edit')->name('rooms.edit');

        // Users & Settings
        Route::livewire('/users',    'users.user-list')->name('users.index');
        Route::livewire('/settings', 'settings')->name('settings');
    });

    // Reservations
    Route::livewire('/reservations', 'reservations.reservation-list')->name('reservations.index');
    Route::livewire('/reservations/create', 'reservations.reservation-create')->name('reservations.create');
    Route::livewire('/reservations/{reservation}/edit', 'reservations.reservation-edit')->name('reservations.edit');

    // Booking Calendar
    Route::livewire('/calendar', 'calendar')->name('calendar');

    // Guests
    Route::livewire('/guests', 'guests.guest-list')->name('guests.index');
    Route::livewire('/guests/create', 'guests.guest-create')->name('guests.create');
    Route::livewire('/guests/{guest}/edit', 'guests.guest-edit')->name('guests.edit');

    // Operations
    Route::livewire('/check-in',  'check-in')->name('checkin.index');
    Route::livewire('/check-out', 'check-out')->name('checkout.index');
    Route::livewire('/invoices',  'invoices.invoice-list')->name('invoices.index');
    Route::livewire('/housekeeping', 'housekeeping.housekeeping-list')->name('housekeeping.index');
    Route::livewire('/maintenance',  'maintenance.maintenance-list')->name('maintenance.index');

    // Reports
    Route::livewire('/reports/daily',     'reports.daily')->name('reports.daily');
    Route::livewire('/reports/occupancy', 'reports.occupancy')->name('reports.occupancy');
    Route::livewire('/reports/revenue',   'reports.revenue')->name('reports.revenue');

    // Invoice PDF actions (controller still needed for DomPDF)
    Route::get('/invoice/download/{id}', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('invoice.download');
    Route::get('/invoice/view/{id}',     [\App\Http\Controllers\InvoiceController::class, 'view'])->name('invoice.view');

    // Daily Cash Sheet PDF actions
    Route::get('/reports/daily-cash-sheet/download',       [\App\Http\Controllers\DailyCashSheetController::class, 'download'])->name('reports.daily-cash-sheet.download');
    Route::get('/reports/daily-cash-sheet/download-range', [\App\Http\Controllers\DailyCashSheetController::class, 'downloadRange'])->name('reports.daily-cash-sheet.download-range');
});
