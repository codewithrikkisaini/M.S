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
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $hotelsList;
    public $showCreateModal = false;
    
    // Wizard step tracking
    public int $currentStep = 1;

    // Step 1: Business Information
    public $name; // Hotel Name
    public $business_name;
    public $owner_name;
    public $tax_id;
    public $company_reg_number;
    public $business_license_number;

    // Step 2: Contact Information
    public $email;
    public $phone;
    public $whatsapp;
    public $website;

    // Step 3: Location
    public $country;
    public $state;
    public $city;
    public $address;
    public $postal_code;
    public $timezone = 'UTC';
    public $currency = 'USD';

    // Step 4: Hotel Information
    public $rooms_count = 10;
    public $category;
    public $property_type;
    public $current_pms;
    public $current_channel_manager;
    public $current_website;

    // Step 5: Administrator
    public $admin_name;
    public $admin_email;
    public $admin_password;

    // Step 6: Subscription
    public $subscription_plan = 'trial';

    public function mount(): void
    {
        $this->loadHotels();
    }

    public function loadHotels(): void
    {
        $this->hotelsList = Hotel::latest()->get();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
        $this->currentStep = 1;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function resetForm(): void
    {
        $this->currentStep = 1;
        $this->name = '';
        $this->business_name = '';
        $this->owner_name = '';
        $this->tax_id = '';
        $this->company_reg_number = '';
        $this->business_license_number = '';
        $this->email = '';
        $this->phone = '';
        $this->whatsapp = '';
        $this->website = '';
        $this->country = '';
        $this->state = '';
        $this->city = '';
        $this->address = '';
        $this->postal_code = '';
        $this->timezone = 'UTC';
        $this->currency = 'USD';
        $this->rooms_count = 10;
        $this->category = '';
        $this->property_type = '';
        $this->current_pms = '';
        $this->current_channel_manager = '';
        $this->current_website = '';
        $this->admin_name = '';
        $this->admin_email = '';
        $this->admin_password = '';
        $this->subscription_plan = 'trial';
    }

    public function nextStep(): void
    {
        if ($this->currentStep === 1) {
            $this->validate([
                'name' => 'required|string|max:255',
                'business_name' => 'nullable|string|max:255',
                'owner_name' => 'nullable|string|max:255',
                'tax_id' => 'nullable|string|max:100',
                'company_reg_number' => 'nullable|string|max:100',
                'business_license_number' => 'nullable|string|max:100',
            ]);
        } elseif ($this->currentStep === 2) {
            $this->validate([
                'email' => 'required|email|unique:hotels,email',
                'phone' => 'nullable|string|max:20',
                'whatsapp' => 'nullable|string|max:20',
                'website' => 'nullable|string|url|max:255',
            ]);
        } elseif ($this->currentStep === 3) {
            $this->validate([
                'country' => 'required|string|max:100',
                'state' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'address' => 'required|string|max:500',
                'postal_code' => 'nullable|string|max:20',
                'timezone' => 'required|string',
                'currency' => 'required|string|max:10',
            ]);
        } elseif ($this->currentStep === 4) {
            $this->validate([
                'rooms_count' => 'required|integer|min:1|max:500',
                'category' => 'nullable|string',
                'property_type' => 'nullable|string',
                'current_pms' => 'nullable|string',
                'current_channel_manager' => 'nullable|string',
                'current_website' => 'nullable|string|url',
            ]);
        } elseif ($this->currentStep === 5) {
            $this->validate([
                'admin_name' => 'required|string|max:255',
                'admin_email' => 'required|email|unique:users,email',
                'admin_password' => 'required|string|min:6',
            ]);
        }

        $this->currentStep++;
    }

    public function prevStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function saveHotel(): void
    {
        $this->validate([
            'subscription_plan' => 'required|string|in:trial,monthly,yearly,lifetime',
        ]);

        // 1. Create Hotel
        $hotel = Hotel::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => 'approved',
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

        // 3. Create Admin User
        $adminUser = User::create([
            'name' => $this->admin_name,
            'email' => $this->admin_email,
            'password' => Hash::make($this->admin_password),
            'role_id' => $adminRole->id,
            'status' => 'active',
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

        // 6. Create SaaS Subscription
        $plan = SubscriptionPlan::where('slug', $this->subscription_plan)->first();
        if ($plan) {
            $now = now();
            Subscription::create([
                'hotel_id' => $hotel->id,
                'subscription_plan_id' => $plan->id,
                'status' => $plan->slug === 'trial' ? 'trialing' : 'active',
                'starts_at' => $now,
                'ends_at' => $plan->slug === 'trial' ? $now->copy()->addDays($plan->trial_days) : $now->copy()->addMonth(),
                'trial_ends_at' => $plan->slug === 'trial' ? $now->copy()->addDays($plan->trial_days) : null,
            ]);
        }

        $this->closeCreateModal();
        $this->loadHotels();
        
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Hotel, default rooms, admin account, and subscription created successfully!"
        ]);
    }

    public function suspendHotel($id): void
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->update(['status' => 'suspended']);

        // Suspend the associated users
        User::withoutGlobalScope('tenant')
            ->where('hotel_id', $hotel->id)
            ->update(['status' => 'inactive']);

        $this->loadHotels();
        
        $this->dispatch('toast', [
            'type' => 'warning',
            'message' => "Hotel '{$hotel->name}' has been suspended. Admin accounts deactivated."
        ]);
    }

    public function unsuspendHotel($id): void
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->update(['status' => 'approved']);

        // Reactivate associated users
        User::withoutGlobalScope('tenant')
            ->where('hotel_id', $hotel->id)
            ->update(['status' => 'active']);

        $this->loadHotels();
        
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Hotel '{$hotel->name}' has been unsuspended and activated."
        ]);
    }

    public function loginAsHotelAdmin($id): mixed
    {
        $adminRole = Role::where('slug', 'admin')->first();
        if (!$adminRole) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Admin role not found.']);
            return null;
        }

        $adminUser = User::withoutGlobalScope('tenant')
            ->where('hotel_id', $id)
            ->where('role_id', $adminRole->id)
            ->first();

        if (!$adminUser) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'No Admin User found for this hotel.']);
            return null;
        }

        // Login as the tenant admin
        Auth::login($adminUser);

        // Put a session flag indicating impersonation (optional)
        session()->put('impersonated_by', 'superadmin');

        return redirect()->route('dashboard');
    }

    public function approveHotel($id): void
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->update(['status' => 'approved']);

        // Activate the admin user for this hotel
        User::withoutGlobalScope('tenant')
            ->where('hotel_id', $hotel->id)
            ->update(['status' => 'active']);

        $this->loadHotels();
        
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Hotel '{$hotel->name}' has been approved and its Admin account activated."
        ]);
    }

    public function rejectHotel($id): void
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->update(['status' => 'rejected']);

        User::withoutGlobalScope('tenant')
            ->where('hotel_id', $hotel->id)
            ->update(['status' => 'inactive']);

        $this->loadHotels();
        
        $this->dispatch('toast', [
            'type' => 'warning',
            'message' => "Hotel '{$hotel->name}' registration application has been rejected."
        ]);
    }

    public function deleteHotel($id): void
    {
        $hotel = Hotel::findOrFail($id);
        $hotelName = $hotel->name;

        // Delete all associated hotel data to prevent foreign key errors
        User::withoutGlobalScope('tenant')->where('hotel_id', $hotel->id)->delete();
        Setting::where('hotel_id', $hotel->id)->delete();
        Room::where('hotel_id', $hotel->id)->delete();
        RoomType::where('hotel_id', $hotel->id)->delete();
        Subscription::where('hotel_id', $hotel->id)->delete();
        $hotel->delete();

        $this->loadHotels();
        
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => "Hotel '{$hotelName}' and all its associated data have been completely deleted."
        ]);
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
