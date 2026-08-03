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
use App\Services\NotificationService;
use App\Mail\NewHotelRegistration;
use App\Mail\HotelRegistrationReceived;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

new class extends Component
{
    public $currentStep = 1;
    public $totalSteps = 5;

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
    public $rooms_count = 0;
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

    public function nextStep()
    {
        if ($this->currentStep == 1) {
            $this->validate([
                'name' => 'required|string|max:255',
                'business_name' => 'nullable|string|max:255',
                'owner_name' => 'nullable|string|max:255',
                'tax_id' => 'nullable|string|max:100',
                'company_reg_number' => 'nullable|string|max:100',
            ]);
        } elseif ($this->currentStep == 2) {
            $this->validate([
                'email' => 'required|email|unique:hotels,email',
                'phone' => 'nullable|string|max:20',
                'whatsapp' => 'nullable|string|max:20',
                'website' => 'nullable|string|max:255',
            ]);
        } elseif ($this->currentStep == 3) {
            $this->validate([
                'country' => 'required|string|max:100',
                'state' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'address' => 'required|string|max:500',
                'postal_code' => 'nullable|string|max:20',
                'timezone' => 'required|string',
                'currency' => 'required|string|max:10',
            ]);
        } elseif ($this->currentStep == 4) {
            $this->validate([
                'rooms_count' => 'nullable|integer|min:0|max:500',
                'category' => 'nullable|string',
                'property_type' => 'nullable|string',
                'current_pms' => 'nullable|string',
            ]);
        }

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function registerHotel(): void
    {
        // Final validation for Step 5
        $this->validate([
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

        // 3. Create Admin User with status = 'active'
        $adminUser = User::create([
            'name' => $this->admin_name,
            'email' => $this->admin_email,
            'password' => Hash::make($this->admin_password),
            'role_id' => $adminRole->id,
            'status' => 'active',
            'hotel_id' => $hotel->id,
        ]);

        // 4. Provision default Room Type (rooms will be added manually by hotel admin)
        RoomType::firstOrCreate([
            'name' => 'Standard Room',
            'hotel_id' => $hotel->id,
        ]);

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

        // 7. Notify Super Admins via DB Notification
        NotificationService::notifySuperAdmins(
            'New Hotel Registration',
            "{$this->admin_name} ne ek naya hotel '{$this->name}' register kiya hai. Please review karke approve karein.",
            '/superadmin/hotels'
        );

        // 8. Send Dynamic Email Notifications
        try {
            // Send email dynamically to all Super Admin users in the database + info@admin.lodgiko.com
            $superadminEmails = User::whereHas('role', function ($query) {
                $query->where('slug', 'superadmin');
            })->pluck('email')->filter()->unique()->toArray();

            if (!in_array('info@admin.lodgiko.com', $superadminEmails)) {
                $superadminEmails[] = 'info@admin.lodgiko.com';
            }

            Mail::to($superadminEmails)->send(new NewHotelRegistration($hotel, $adminUser));
        } catch (\Exception $e) {
            Log::error('Failed to send registration emails: ' . $e->getMessage());
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
