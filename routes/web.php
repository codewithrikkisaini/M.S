<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ─── Public ────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Setup Route ───────────────────────────────────────────────────────────
Route::get('/setup-project', function () {
    try {
        $output = [];

        // 1. Run migrations
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output[] = "Migrations run successfully: " . \Illuminate\Support\Facades\Artisan::output();

        // 2. Adjust settings table unique index
        try {
            \Illuminate\Support\Facades\Schema::table('settings', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->dropUnique('settings_key_unique');
            });
            $output[] = "Dropped old settings_key_unique index.";
        } catch (\Exception $e) {
            $output[] = "Unique index drop ignored/failed: " . $e->getMessage();
        }

        try {
            \Illuminate\Support\Facades\Schema::table('settings', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->unique(['key', 'hotel_id']);
            });
            $output[] = "Created new settings composite unique index.";
        } catch (\Exception $e) {
            $output[] = "Composite unique index creation ignored/failed: " . $e->getMessage();
        }

        // 3. Seed Roles
        $superadminRole = \App\Models\Role::firstOrCreate(['slug' => 'superadmin'], ['name' => 'Super Admin']);
        $adminRole = \App\Models\Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $receptionistRole = \App\Models\Role::firstOrCreate(['slug' => 'receptionist'], ['name' => 'Receptionist']);
        $output[] = "Roles seeded/verified.";

        // 4. Seed Hotels
        $hotel = \App\Models\Hotel::firstOrCreate(
            ['email' => 'grandplaza@merahkie.com'],
            [
                'name' => 'Grand Plaza Hotel',
                'phone' => '+1234567890',
                'address' => '123 Luxury Avenue',
                'status' => 'approved'
            ]
        );
        $output[] = "Default Hotel 'Grand Plaza Hotel' seeded/verified.";

        // 5. Seed Users
        // Super Admin
        $superadmin = \App\Models\User::updateOrCreate(
            ['email' => 'superadmin@merahkie.com'],
            [
                'name' => 'Super Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role_id' => $superadminRole->id,
                'status' => 'active',
                'hotel_id' => null
            ]
        );
        $output[] = "Super Admin user seeded/verified.";

        // Hotel Admin
        $adminUser = \App\Models\User::updateOrCreate(
            ['email' => 'admin@merahkie.com'],
            [
                'name' => 'Grand Plaza Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role_id' => $adminRole->id,
                'status' => 'active',
                'hotel_id' => $hotel->id
            ]
        );
        $output[] = "Hotel Admin user seeded/verified.";

        // Receptionist
        $receptionistUser = \App\Models\User::updateOrCreate(
            ['email' => 'receptionist@merahkie.com'],
            [
                'name' => 'Grand Plaza Reception',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role_id' => $receptionistRole->id,
                'status' => 'active',
                'hotel_id' => $hotel->id
            ]
        );
        $output[] = "Receptionist user seeded/verified.";

        // 6. Seed Default Settings for Hotel
        $defaults = [
            ['key' => 'hotel_name',      'value' => 'Grand Plaza Hotel'],
            ['key' => 'hotel_address',   'value' => '123 Luxury Avenue'],
            ['key' => 'hotel_phone',     'value' => '+1234567890'],
            ['key' => 'hotel_email',     'value' => 'grandplaza@merahkie.com'],
            ['key' => 'hotel_website',   'value' => 'www.grandplazahotel.com'],
            ['key' => 'hotel_timezone',  'value' => 'UTC'],
            ['key' => 'currency',        'value' => 'USD'],
            ['key' => 'date_format',     'value' => 'd M Y'],
            ['key' => 'checkin_time',    'value' => '14:00'],
            ['key' => 'checkout_time',   'value' => '12:00'],
            ['key' => 'email_notifications', 'value' => '1'],
            ['key' => 'sms_notifications',   'value' => '0'],
            ['key' => 'invoice_prefix',  'value' => 'INV-'],
            ['key' => 'invoice_footer',  'value' => 'Thank you for staying with us!'],
        ];

        foreach ($defaults as $row) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $row['key'], 'hotel_id' => $hotel->id],
                ['value' => $row['value']]
            );
        }
        $output[] = "Default settings for Grand Plaza Hotel seeded.";

        return response()->json([
            'success' => true,
            'log' => $output
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

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
        Route::livewire('/users', 'users.user-list')->name('users.index');
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
    Route::livewire('/check-in', 'check-in')->name('checkin.index');
    Route::livewire('/check-out', 'check-out')->name('checkout.index');
    Route::livewire('/invoices', 'invoices.invoice-list')->name('invoices.index');
    Route::livewire('/housekeeping', 'housekeeping.housekeeping-list')->name('housekeeping.index');
    Route::livewire('/maintenance', 'maintenance.maintenance-list')->name('maintenance.index');

    // Reports
    Route::livewire('/reports/daily', 'reports.daily')->name('reports.daily');
    Route::livewire('/reports/occupancy', 'reports.occupancy')->name('reports.occupancy');
    Route::livewire('/reports/revenue', 'reports.revenue')->name('reports.revenue');

    // Invoice PDF actions (controller still needed for DomPDF)
    Route::get('/invoice/download/{id}', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('invoice.download');
    Route::get('/invoice/view/{id}', [\App\Http\Controllers\InvoiceController::class, 'view'])->name('invoice.view');

    // Daily Cash Sheet PDF actions
    Route::get('/reports/daily-cash-sheet/download', [\App\Http\Controllers\DailyCashSheetController::class, 'download'])->name('reports.daily-cash-sheet.download');
    Route::get('/reports/daily-cash-sheet/download-range', [\App\Http\Controllers\DailyCashSheetController::class, 'downloadRange'])->name('reports.daily-cash-sheet.download-range');
});
