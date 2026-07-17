<?php

use Livewire\Component;
use App\Models\Hotel;
use App\Models\User;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;

new class extends Component
{
    // Form fields
    public $name;
    public $email;
    public $phone;
    public $address;
    
    public $admin_name;
    public $admin_email;
    public $admin_password;
    public $admin_password_confirmation;

    public $successMessage = false;

    public function registerHotel(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:hotels,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:6|confirmed',
        ]);

        // 1. Create Hotel with status = 'pending'
        $hotel = Hotel::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => 'pending',
        ]);

        // 2. Find admin role
        $adminRole = Role::where('slug', 'admin')->first();

        // 3. Create Admin User with status = 'inactive' (until hotel is approved)
        User::create([
            'name' => $this->admin_name,
            'email' => $this->admin_email,
            'password' => Hash::make($this->admin_password),
            'role_id' => $adminRole->id,
            'status' => 'inactive',
            'hotel_id' => $hotel->id,
        ]);

        // 4. Seed default settings for the hotel
        $defaults = [
            ['key' => 'hotel_name',      'value' => $hotel->name],
            ['key' => 'hotel_address',   'value' => $hotel->address ?? ''],
            ['key' => 'hotel_phone',     'value' => $hotel->phone ?? ''],
            ['key' => 'hotel_email',     'value' => $hotel->email],
            ['key' => 'hotel_website',   'value' => ''],
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
            Setting::create([
                'key' => $row['key'],
                'value' => $row['value'],
                'hotel_id' => $hotel->id,
            ]);
        }

        $this->successMessage = true;
        
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Registration application submitted successfully!"
        ]);
    }

    public function render(): mixed
    {
        return $this->view()->layout('layouts.guest');
    }
};
