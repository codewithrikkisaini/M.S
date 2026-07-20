<?php

use Livewire\Component;
use App\Models\Hotel;
use App\Models\User;
use App\Models\Role;
use App\Models\Setting;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;

new class extends Component
{
    // 1. Business Information
    public $name; // Hotel Trade Name
    public $business_name;
    public $owner_name;
    public $tax_id;
    public $company_reg_number;
    public $business_license_number;

    // 2. Contact Information
    public $email;
    public $phone;
    public $whatsapp;
    public $website;

    // 3. Location & Region
    public $country = 'United States';
    public $state;
    public $city;
    public $address;
    public $postal_code;
    public $timezone = 'UTC';
    public $currency = 'USD';

    // 4. Property Profile & Migration
    public $rooms_count = 10;
    public $category = '4-star';
    public $property_type = 'Boutique Hotel';
    public $current_pms;
    public $current_channel_manager;
    public $current_website;

    // 5. Administrator Account
    public $admin_name;
    public $admin_email;
    public $admin_password;
    public $admin_password_confirmation;

    public $successMessage = false;

    public function registerHotel(): void
    {
        $this->validate([
            // Step 1
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:100',
            'company_reg_number' => 'nullable|string|max:100',
            'business_license_number' => 'nullable|string|max:100',

            // Step 2
            'email' => 'required|email|unique:hotels,email',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',

            // Step 3
            'country' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'required|string|max:500',
            'postal_code' => 'nullable|string|max:20',
            'timezone' => 'required|string',
            'currency' => 'required|string|max:10',

            // Step 4
            'rooms_count' => 'required|integer|min:1|max:500',
            'category' => 'nullable|string',
            'property_type' => 'nullable|string',
            'current_pms' => 'nullable|string',
            'current_channel_manager' => 'nullable|string',
            'current_website' => 'nullable|string',

            // Step 5
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:6|confirmed',
        ]);

        // 1. Create Hotel with status = 'pending' and complete dataset
        $hotel = Hotel::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => 'pending',
            'business_name' => $this->business_name,
            'owner_name' => $this->owner_name,
            'tax_id' => $this->tax_id,
            'company_reg_number' => $this->company_reg_number,
            'business_license_number' => $this->business_license_number,
            'whatsapp' => $this->whatsapp,
            'website' => $this->website,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'timezone' => $this->timezone,
            'currency' => $this->currency,
            'rooms_count' => $this->rooms_count,
            'category' => $this->category,
            'property_type' => $this->property_type,
            'current_pms' => $this->current_pms,
            'current_channel_manager' => $this->current_channel_manager,
            'current_website' => $this->current_website,
        ]);

        // 2. Find admin role
        $adminRole = Role::where('slug', 'admin')->first();

        // 3. Create Admin User with status = 'inactive' (until superadmin approves)
        $adminUser = User::create([
            'name' => $this->admin_name,
            'email' => $this->admin_email,
            'password' => Hash::make($this->admin_password),
            'role_id' => $adminRole->id,
            'status' => 'inactive',
            'hotel_id' => $hotel->id,
        ]);

        // 4. Provision default Room Type and Rooms
        $roomType = RoomType::create([
            'name' => 'Standard Room',
            'hotel_id' => $hotel->id,
        ]);

        for ($i = 1; $i <= $this->rooms_count; $i++) {
            $roomNum = 100 + $i;
            if ($i > 10) {
                $roomNum = 200 + ($i - 10);
            }
            Room::create([
                'room_number' => 'Room ' . $roomNum,
                'room_type_id' => $roomType->id,
                'price' => 100.00,
                'status' => 'Available',
                'floor' => $i <= 10 ? '1st Floor' : '2nd Floor',
                'hotel_id' => $hotel->id,
            ]);
        }

        // 5. Seed default settings for the hotel
        $defaults = [
            ['key' => 'hotel_name',      'value' => $hotel->name],
            ['key' => 'hotel_address',   'value' => $hotel->address ?? ''],
            ['key' => 'hotel_phone',     'value' => $hotel->phone ?? ''],
            ['key' => 'hotel_email',     'value' => $hotel->email],
            ['key' => 'hotel_website',   'value' => $hotel->website ?? ''],
            ['key' => 'hotel_timezone',  'value' => $hotel->timezone],
            ['key' => 'currency',        'value' => $hotel->currency],
            ['key' => 'date_format',     'value' => 'd M Y'],
            ['key' => 'checkin_time',    'value' => '14:00'],
            ['key' => 'checkout_time',   'value' => '12:00'],
            ['key' => 'email_notifications', 'value' => '1'],
            ['key' => 'sms_notifications',   'value' => '0'],
            ['key' => 'invoice_prefix',  'value' => 'INV-'],
            ['key' => 'invoice_footer',  'value' => 'Thank you for staying with us!'],
        ];

        foreach ($defaults as $row) {
            Setting::create([
                'key' => $row['key'],
                'value' => $row['value'],
                'hotel_id' => $hotel->id,
            ]);
        }

        // 6. Default Trial Subscription
        $trialPlan = SubscriptionPlan::where('slug', 'trial')->first();
        if ($trialPlan) {
            $now = now();
            Subscription::create([
                'hotel_id' => $hotel->id,
                'subscription_plan_id' => $trialPlan->id,
                'status' => 'trialing',
                'starts_at' => $now,
                'ends_at' => $now->copy()->addDays($trialPlan->trial_days ?: 14),
                'trial_ends_at' => $now->copy()->addDays($trialPlan->trial_days ?: 14),
            ]);
        }

        $this->successMessage = true;
        
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Complete registration application submitted successfully!"
        ]);
    }

    public function render(): mixed
    {
        return $this->view()->layout('layouts.guest');
    }
};
