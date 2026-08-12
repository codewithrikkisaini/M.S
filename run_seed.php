<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$output = "Seeding log:\n";

try {
    // 1. Seed Roles
    $superadminRole = \App\Models\Role::updateOrCreate(['slug' => 'superadmin'], ['name' => 'Super Admin']);
    $adminRole = \App\Models\Role::updateOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
    $receptionistRole = \App\Models\Role::updateOrCreate(['slug' => 'receptionist'], ['name' => 'Receptionist']);
    $output .= "- Roles seeded.\n";

    // 2. Seed Hotels
    $hotel1 = \App\Models\Hotel::updateOrCreate(
        ['email' => 'grandplaza@merahkie.com'],
        [
            'name' => 'Grand Plaza Hotel',
            'phone' => '+1234567890',
            'address' => '123 Luxury Avenue',
            'status' => 'approved'
        ]
    );
    $output .= "- Hotel 1 seeded: ID " . $hotel1->id . " (" . $hotel1->name . ")\n";

    $hotel2 = \App\Models\Hotel::updateOrCreate(
        ['email' => 'contact@sunsetbeach.com'],
        [
            'name' => 'Sunset Beach Resort',
            'phone' => '+1987654321',
            'address' => '456 Ocean Boulevard',
            'status' => 'approved'
        ]
    );
    $output .= "- Hotel 2 seeded: ID " . $hotel2->id . " (" . $hotel2->name . ")\n";

    $hotel3 = \App\Models\Hotel::updateOrCreate(
        ['email' => 'contact@royalcrown.com'],
        [
            'name' => 'Royal Crown Hotel',
            'phone' => '+1555444333',
            'address' => '789 Crown Street',
            'status' => 'approved'
        ]
    );
    $output .= "- Hotel 3 seeded: ID " . $hotel3->id . " (" . $hotel3->name . ")\n";

    // 3. Seed Users
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
    $output .= "- Super Admin seeded: " . $superadmin->email . "\n";

    $adminUser = \App\Models\User::updateOrCreate(
        ['email' => 'admin@merahkie.com'],
        [
            'name' => 'Grand Plaza Admin',
            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
            'role_id' => $adminRole->id,
            'status' => 'active',
            'hotel_id' => $hotel1->id
        ]
    );
    $output .= "- Hotel Admin seeded: " . $adminUser->email . "\n";

    // Receptionist for Hotel 1 (Grand Plaza Hotel)
    $receptionistUser1 = \App\Models\User::updateOrCreate(
        ['email' => 'receptionist@merahkie.com'],
        [
            'name' => 'Grand Plaza Reception',
            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
            'role_id' => $receptionistRole->id,
            'status' => 'active',
            'hotel_id' => $hotel1->id
        ]
    );
    $output .= "- Receptionist 1 (Grand Plaza) seeded: " . $receptionistUser1->email . "\n";

    // Receptionist for Hotel 2 (Sunset Beach Resort)
    $receptionistUser2 = \App\Models\User::updateOrCreate(
        ['email' => 'receptionist2@sunsetbeach.com'],
        [
            'name' => 'Sunset Beach Reception',
            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
            'role_id' => $receptionistRole->id,
            'status' => 'active',
            'hotel_id' => $hotel2->id
        ]
    );
    $output .= "- Receptionist 2 (Sunset Beach) seeded: " . $receptionistUser2->email . "\n";

    // Receptionist for Hotel 3 (Royal Crown Hotel)
    $receptionistUser3 = \App\Models\User::updateOrCreate(
        ['email' => 'receptionist3@royalcrown.com'],
        [
            'name' => 'Royal Crown Reception',
            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
            'role_id' => $receptionistRole->id,
            'status' => 'active',
            'hotel_id' => $hotel3->id
        ]
    );
    $output .= "- Receptionist 3 (Royal Crown) seeded: " . $receptionistUser3->email . "\n";

    // 4. Seed Settings
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
            ['key' => $row['key'], 'hotel_id' => $hotel1->id],
            ['value' => $row['value']]
        );
        \App\Models\Setting::updateOrCreate(
            ['key' => $row['key'], 'hotel_id' => $hotel2->id],
            ['value' => $row['key'] === 'hotel_name' ? 'Sunset Beach Resort' : $row['value']]
        );
        \App\Models\Setting::updateOrCreate(
            ['key' => $row['key'], 'hotel_id' => $hotel3->id],
            ['value' => $row['key'] === 'hotel_name' ? 'Royal Crown Hotel' : $row['value']]
        );
    }
    $output .= "- Settings seeded for all hotels.\n";

    // 5. Seed Subscription Plans
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
    $output .= "- Subscription plans seeded.\n";

    // 6. Subscription & Invoice for all hotels
    $trialPlan = $seededPlans['trial'];
    $now = now();

    foreach ([$hotel1, $hotel2, $hotel3] as $h) {
        \App\Models\Subscription::updateOrCreate(
            ['hotel_id' => $h->id],
            [
                'subscription_plan_id' => $trialPlan->id,
                'status' => 'trialing',
                'starts_at' => $now,
                'ends_at' => $now->copy()->addDays($trialPlan->trial_days),
                'trial_ends_at' => $now->copy()->addDays($trialPlan->trial_days),
            ]
        );
    }
    $output .= "- Subscriptions seeded for all hotels.\n";
    $output .= "=== SUCCESS ===\n";

} catch (\Exception $e) {
    $output .= "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}

file_put_contents(__DIR__ . '/seed_log.txt', $output);
echo "Done\n";
