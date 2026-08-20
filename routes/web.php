<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicHotelController;


// ─── Public ────────────────────────────────────────────────────────────────
Route::get('/', function () {
    $hotels = \App\Models\Hotel::where('status', 'approved')->with(['images', 'rooms' => function ($q) {
        $q->withoutGlobalScope('tenant');
    }])->get();
    return view('welcome', compact('hotels'));
});
Route::get('/hotel/{slug}', [PublicHotelController::class, 'show'])->name('hotel.show');
Route::get('/hotel/{slug}/reserve/{room?}', [PublicHotelController::class, 'reserveRoom'])->name('hotel.reserve');
Route::get('/booking/search', [PublicHotelController::class, 'search'])->name('booking.search');
Route::post('/hotel/book-instant', [PublicHotelController::class, 'bookInstant'])->name('hotel.book-instant');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/receptionist/login', function () { return redirect()->route('login'); })->name('receptionist.login');
Route::post('/receptionist/login', function () { return redirect()->route('login'); });
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/git-check', function () {
    return 'Git Upload Working - ' . now();
});

Route::get('/clear-records', function () {
    if (!app()->environment('local')) {
        if (!auth()->check() || (!auth()->user()->hasRole('superadmin') && !auth()->user()->hasRole('admin'))) {
            abort(403, 'Unauthorized. This action is disabled in production unless authorized by Super Admin.');
        }
    }
    try {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \App\Models\Reservation::query()->delete();
        \Illuminate\Support\Facades\DB::table('reservation_rooms')->delete();
        \App\Models\Guest::query()->delete();
        \App\Models\Invoice::query()->delete();
        \App\Models\Payment::query()->delete();
        \App\Models\Checkin::query()->delete();
        \App\Models\Checkout::query()->delete();
        \App\Models\Housekeeping::query()->delete();
        \App\Models\MaintenanceTicket::query()->delete();
        \App\Models\HotelImage::query()->delete();
        \App\Models\ActivityLog::query()->delete();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        return response()->json([
            'status' => 'success',
            'message' => 'All test reservations, guests, invoices, payments, activity logs, and gallery records have been completely cleared!'
        ]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

Route::get('/seed-rooms', function () {
    try {
        $seeder = new \Database\Seeders\RoomsSeeder();
        $seeder->run();

        $totalRooms = \App\Models\Room::withoutGlobalScope('tenant')->count();
        $hotelRooms = \App\Models\Hotel::withCount(['rooms' => function ($q) {
            $q->withoutGlobalScope('tenant');
        }])->get(['id', 'name', 'slug', 'rooms_count']);

        return response()->json([
            'status' => 'success',
            'message' => "Successfully seeded {$totalRooms} dynamic rooms across all hotels!",
            'total_rooms_in_system' => $totalRooms,
            'hotels' => $hotelRooms,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});

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

        // 2b. Adjust rooms table unique index
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE rooms DROP INDEX rooms_room_number_unique");
            $output[] = "Dropped old rooms_room_number_unique index.";
        } catch (\Exception $e) {
            $output[] = "Unique index drop ignored/failed: " . $e->getMessage();
        }

        try {
            \Illuminate\Support\Facades\Schema::table('rooms', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->unique(['hotel_id', 'room_number'], 'rooms_hotel_id_room_number_unique');
            });
            $output[] = "Created new rooms composite unique index (hotel_id, room_number).";
        } catch (\Exception $e) {
            $output[] = "Composite unique index creation ignored/failed: " . $e->getMessage();
        }

        // 3. Seed Roles
        $superadminRole = \App\Models\Role::firstOrCreate(['slug' => 'superadmin'], ['name' => 'Super Admin']);
        $adminRole = \App\Models\Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $receptionistRole = \App\Models\Role::firstOrCreate(['slug' => 'receptionist'], ['name' => 'Receptionist']);
        $housekeepingRole = \App\Models\Role::firstOrCreate(['slug' => 'housekeeping'], ['name' => 'Housekeeping Staff']);
        $maintenanceRole = \App\Models\Role::firstOrCreate(['slug' => 'maintenance'], ['name' => 'Maintenance Staff']);
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
        // Super Admin 1
        \App\Models\User::updateOrCreate(
            ['email' => 'superadmin@merahkie.com'],
            [
                'name' => 'Super Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role_id' => $superadminRole->id,
                'status' => 'active',
                'hotel_id' => null
            ]
        );

        // Super Admin 2
        \App\Models\User::updateOrCreate(
            ['email' => 'rikkisaini4455@gmail.com'],
            [
                'name' => 'Super Admin (Rikki)',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role_id' => $superadminRole->id,
                'status' => 'active',
                'hotel_id' => null
            ]
        );
        $output[] = "Super Admin users seeded/verified.";

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

        // Housekeeping Staff
        \App\Models\User::updateOrCreate(
            ['email' => 'housekeeping@merahkie.com'],
            [
                'name' => 'John Doe',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role_id' => $housekeepingRole->id,
                'status' => 'active',
                'hotel_id' => $hotel->id
            ]
        );
        $output[] = "Housekeeping user seeded/verified.";

        // Maintenance Staff
        \App\Models\User::updateOrCreate(
            ['email' => 'maintenance@merahkie.com'],
            [
                'name' => 'Mike Johnson',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role_id' => $maintenanceRole->id,
                'status' => 'active',
                'hotel_id' => $hotel->id
            ]
        );
        $output[] = "Maintenance user seeded/verified.";

        // 6. Seed Default Settings for Hotel
        $defaults = [
            ['key' => 'hotel_name', 'value' => 'Grand Plaza Hotel'],
            ['key' => 'hotel_address', 'value' => '123 Luxury Avenue'],
            ['key' => 'hotel_phone', 'value' => '+1234567890'],
            ['key' => 'hotel_email', 'value' => 'grandplaza@merahkie.com'],
            ['key' => 'hotel_website', 'value' => 'www.grandplazahotel.com'],
            ['key' => 'hotel_timezone', 'value' => 'UTC'],
            ['key' => 'currency', 'value' => 'USD'],
            ['key' => 'date_format', 'value' => 'd M Y'],
            ['key' => 'checkin_time', 'value' => '14:00'],
            ['key' => 'checkout_time', 'value' => '12:00'],
            ['key' => 'email_notifications', 'value' => '1'],
            ['key' => 'sms_notifications', 'value' => '0'],
            ['key' => 'invoice_prefix', 'value' => 'INV-'],
            ['key' => 'invoice_footer', 'value' => 'Thank you for staying with us!'],
        ];

        foreach ($defaults as $row) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $row['key'], 'hotel_id' => $hotel->id],
                ['value' => $row['value']]
            );
        }
        $output[] = "Default settings for Grand Plaza Hotel seeded.";

        // 7. Seed SaaS Subscription Plans
        $plans = [
            [
                'name' => 'Trial Plan',
                'slug' => 'trial',
                'price' => 0.00,
                'billing_cycle' => 'trial',
                'trial_days' => 14,
                'max_rooms' => 5,
                'max_users' => 2,
                'description' => '14-day free trial to explore our platform features.',
                'status' => 'active',
            ],
            [
                'name' => 'Monthly Pro',
                'slug' => 'monthly',
                'price' => 29.00,
                'billing_cycle' => 'monthly',
                'trial_days' => 0,
                'max_rooms' => 25,
                'max_users' => 10,
                'description' => 'Perfect for small and medium-sized hotels.',
                'status' => 'active',
            ],
            [
                'name' => 'Yearly Premium',
                'slug' => 'yearly',
                'price' => 249.00,
                'billing_cycle' => 'yearly',
                'trial_days' => 0,
                'max_rooms' => 100,
                'max_users' => 30,
                'description' => 'Great value for growing hotel networks.',
                'status' => 'active',
            ],
            [
                'name' => 'Lifetime Enterprise',
                'slug' => 'lifetime',
                'price' => 999.00,
                'billing_cycle' => 'lifetime',
                'trial_days' => 0,
                'max_rooms' => null,
                'max_users' => null,
                'description' => 'Unlimited access for lifetime with premium support.',
                'status' => 'active',
            ],
        ];

        $seededPlans = [];
        foreach ($plans as $p) {
            $seededPlans[$p['slug']] = \App\Models\SubscriptionPlan::updateOrCreate(
                ['slug' => $p['slug']],
                $p
            );
        }
        $output[] = "SaaS subscription plans seeded.";

        // 8. Seed Subscription for Default Hotel
        $trialPlan = $seededPlans['trial'];
        $now = now();
        $subscription = \App\Models\Subscription::updateOrCreate(
            ['hotel_id' => $hotel->id],
            [
                'subscription_plan_id' => $trialPlan->id,
                'status' => 'trialing',
                'starts_at' => $now,
                'ends_at' => $now->copy()->addDays($trialPlan->trial_days),
                'trial_ends_at' => $now->copy()->addDays($trialPlan->trial_days),
            ]
        );
        $output[] = "Default subscription for Grand Plaza Hotel seeded.";

        // 9. Seed some subscription invoices for Grand Plaza Hotel
        \App\Models\SubscriptionInvoice::updateOrCreate(
            ['invoice_number' => 'SUB-2026-0001', 'hotel_id' => $hotel->id],
            [
                'subscription_plan_id' => $trialPlan->id,
                'amount' => 0.00,
                'status' => 'paid',
                'billing_date' => $now->copy()->subDays(2)->format('Y-m-d'),
                'due_date' => $now->copy()->subDays(2)->format('Y-m-d'),
                'paid_at' => $now->copy()->subDays(2),
                'payment_method' => 'Free',
            ]
        );
        $output[] = "Default subscription invoices seeded.";

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

// ─── Public Registration & Booking ─────────────────────────────────────────
Route::get('/register-hotel', [\App\Http\Controllers\HotelRegistrationController::class, 'showForm'])->name('register-hotel');
Route::post('/register-hotel', [\App\Http\Controllers\HotelRegistrationController::class, 'register'])->name('register-hotel.post');
Route::get('/register-hotel/success', [\App\Http\Controllers\HotelRegistrationController::class, 'success'])->name('register-hotel.success');

// ─── PayPal Billing & Webhook Routes ──────────────────────────────
Route::get('/billing/pay/{invoice}', [\App\Http\Controllers\PayPalController::class, 'showPaymentPage'])->name('billing.pay');
Route::post('/billing/paypal/{invoice}/create-order', [\App\Http\Controllers\PayPalController::class, 'createOrder'])->name('billing.paypal.create-order');
Route::post('/billing/paypal/{invoice}/capture-order', [\App\Http\Controllers\PayPalController::class, 'captureOrder'])->name('billing.paypal.capture.api');
Route::get('/billing/paypal/{invoice}/capture', [\App\Http\Controllers\PayPalController::class, 'captureOrder'])->name('billing.paypal.capture');
Route::get('/billing/paypal/{invoice}/success', [\App\Http\Controllers\PayPalController::class, 'success'])->name('billing.paypal.success');
Route::get('/billing/paypal/{invoice}/cancel', [\App\Http\Controllers\PayPalController::class, 'cancel'])->name('billing.paypal.cancel');
Route::post('/api/webhooks/paypal', [\App\Http\Controllers\PayPalController::class, 'handleWebhook'])->name('webhooks.paypal');

Route::get('/book/{hotel_id?}', function ($hotel_id = null) {
    $id = $hotel_id ?: request('hotel_id');
    if ($id) {
        $hotel = \App\Models\Hotel::find($id);
        if ($hotel) {
            return redirect()->route('hotel.show', ['slug' => $hotel->slug ?: $hotel->id]);
        }
    }
    if ($search = request('search')) {
        $hotel = \App\Models\Hotel::where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('city', 'LIKE', '%' . $search . '%')
            ->first();
        if ($hotel) {
            return redirect()->route('hotel.show', ['slug' => $hotel->slug ?: $hotel->id]);
        }
    }
    $firstHotel = \App\Models\Hotel::first();
    if ($firstHotel) {
        return redirect()->route('hotel.show', ['slug' => $firstHotel->slug ?: $firstHotel->id]);
    }
    return redirect('/');
})->name('booking-engine');

Route::get('/hotel/{slug}/book', function ($slug) {
    return redirect()->route('hotel.show', ['slug' => $slug]);
})->name('booking-engine.hotel');

Route::get('/{city}/{slug}/book', function ($city, $slug) {
    return redirect()->route('hotel.show', ['slug' => $slug]);
})->name('booking-engine.seo');
Route::livewire('/track', 'public.track-booking')->name('track-booking');
Route::get('/booking/slip/{pnr}/download', [\App\Http\Controllers\BookingSlipController::class, 'download'])->name('booking.slip.download');

// ─── Auth-protected (all MFC via Route::livewire) ──────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::livewire('/dashboard', 'dashboard')->name('dashboard');
    Route::livewire('/receptionist/dashboard', 'dashboard')->name('receptionist.dashboard');
    Route::livewire('/onboarding', 'public.onboarding')->name('onboarding');

    // Super Admin Routes
    Route::middleware('superadmin')->group(function () {
        Route::livewire('/superadmin/dashboard', 'superadmin.dashboard')->name('superadmin.dashboard');
        Route::get('/superadmin/hotels', [\App\Http\Controllers\SuperAdminHotelController::class, 'index'])->name('superadmin.hotels.index');
        Route::put('/superadmin/hotels/{hotel}', [\App\Http\Controllers\SuperAdminHotelController::class, 'update'])->name('superadmin.hotels.update');
        Route::get('/superadmin/hotels/{hotel}', [\App\Http\Controllers\SuperAdminHotelController::class, 'show'])->name('superadmin.hotels.show');
        Route::post('/superadmin/hotels/{hotel}/approve-7day', [\App\Http\Controllers\SuperAdminHotelController::class, 'approve7DayTrial'])->name('superadmin.hotels.approve-7day');
        Route::post('/superadmin/hotels/{hotel}/approve-15day', [\App\Http\Controllers\SuperAdminHotelController::class, 'approve15DayTrial'])->name('superadmin.hotels.approve-15day');
        Route::post('/superadmin/hotels/{hotel}/approve-paid', [\App\Http\Controllers\SuperAdminHotelController::class, 'createPaidSubscription'])->name('superadmin.hotels.approve-paid');
        Route::post('/superadmin/hotels/{hotel}/extend-trial', [\App\Http\Controllers\SuperAdminHotelController::class, 'extendTrial'])->name('superadmin.hotels.extend-trial');
        Route::post('/superadmin/hotels/{hotel}/suspend', [\App\Http\Controllers\SuperAdminHotelController::class, 'suspend'])->name('superadmin.hotels.suspend');
        Route::post('/superadmin/hotels/{hotel}/activate', [\App\Http\Controllers\SuperAdminHotelController::class, 'activate'])->name('superadmin.hotels.activate');
        Route::post('/superadmin/hotels/{hotel}/resend-invoice/{invoice}', [\App\Http\Controllers\SuperAdminHotelController::class, 'resendInvoice'])->name('superadmin.hotels.resend-invoice');
        Route::post('/superadmin/hotels/{hotel}/resend-welcome', [\App\Http\Controllers\SuperAdminHotelController::class, 'resendWelcome'])->name('superadmin.hotels.resend-welcome');
        Route::delete('/superadmin/hotels/{hotel}', [\App\Http\Controllers\SuperAdminHotelController::class, 'destroy'])->name('superadmin.hotels.destroy');
        Route::livewire('/superadmin/saas-plans', 'superadmin.saas-plans')->name('superadmin.saas-plans.index');
        Route::livewire('/superadmin/saas-billing', 'superadmin.saas-billing')->name('superadmin.saas-billing.index');
        Route::livewire('/superadmin/saas-invoices', 'superadmin.saas-invoices')->name('superadmin.saas-invoices.index');
        Route::livewire('/superadmin/global-settings', 'superadmin.global-settings')->name('superadmin.global-settings');

        // Document Management
        Route::livewire('/superadmin/documents', 'superadmin.documents')->name('superadmin.documents.index');
        Route::get('/superadmin/documents/{document}/preview', [\App\Http\Controllers\SuperAdminDocumentController::class, 'preview'])->name('superadmin.document.preview');
        Route::get('/superadmin/documents/{document}/download', [\App\Http\Controllers\SuperAdminDocumentController::class, 'download'])->name('superadmin.document.download');
    });

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
        Route::livewire('/profile', 'profile')->name('profile');
        Route::livewire('/billing', 'billing')->name('billing.index');

        // Hotel Documents
        Route::livewire('/hotel-documents', 'hotel-documents')->name('hotel-documents.index');
        Route::get('/hotel-documents/{document}/preview', [\App\Http\Controllers\DocumentController::class, 'preview'])->name('document.preview');
        Route::get('/hotel-documents/{document}/download', [\App\Http\Controllers\DocumentController::class, 'download'])->name('document.download');

        // Integrations & Enterprise Features
        Route::livewire('/integrations/channels', 'integrations.channel-manager')->name('integrations.channels');
        Route::livewire('/integrations/stripe', 'integrations.stripe-settings')->name('integrations.stripe');
        Route::livewire('/integrations/templates', 'integrations.notification-templates')->name('integrations.templates');
        Route::livewire('/integrations/api', 'integrations.api-management')->name('integrations.api');
        Route::livewire('/enterprise/activity-logs', 'enterprise.activity-logs')->name('enterprise.logs');
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

    // Guest Blacklist
    Route::livewire('/guests/blacklisted', 'guests.blacklist-list')->name('guests.blacklist.index');
    Route::livewire('/guests/blacklist/create', 'guests.blacklist-create')->name('guests.blacklist.create');
    Route::livewire('/guests/blacklist/{blacklist}/edit', 'guests.blacklist-edit')->name('guests.blacklist.edit');
    Route::get('/guests/blacklist/{document}/download', [\App\Http\Controllers\BlacklistDocumentController::class, 'download'])->name('guests.blacklist.document.download');

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
    Route::get('/reports/daily-cash-sheet/customer-pdf/{reservationId}', [\App\Http\Controllers\DailyCashSheetController::class, 'downloadCustomerPdf'])->name('reports.daily-cash-sheet.customer-pdf');



});
