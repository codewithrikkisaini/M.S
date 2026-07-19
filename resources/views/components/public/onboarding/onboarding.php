<?php

use Livewire\Component;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public $step = 1;

    // Step 1: Branding and Locales
    public $hotel_name;
    public $currency = 'USD';
    public $timezone = 'UTC';

    // Step 2: First Room Category
    public $room_type_name = 'Deluxe Room';
    public $base_price = 120.00;
    public $base_occupancy = 2;

    // Step 3: First Room Unit
    public $room_number = '101';
    public $floor = '1';

    public function mount(): void
    {
        $this->hotel_name = Setting::get('hotel_name', 'Grand Plaza Hotel');
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'hotel_name' => 'required|string|max:255',
                'currency' => 'required|string|max:10',
            ]);
        } elseif ($this->step === 2) {
            $this->validate([
                'room_type_name' => 'required|string|max:100',
                'base_price' => 'required|numeric|min:0',
                'base_occupancy' => 'required|integer|min:1',
            ]);
        }
        $this->step++;
    }

    public function prevStep(): void
    {
        $this->step--;
    }

    public function completeOnboarding(): void
    {
        $this->validate([
            'room_number' => 'required|string|max:20',
            'floor' => 'required|string|max:10',
        ]);

        $hotelId = Auth::user()->hotel_id;

        DB::transaction(function () use ($hotelId) {
            // 1. Set general hotel parameters
            Setting::set('hotel_name', $this->hotel_name);
            Setting::set('currency', $this->currency);
            Setting::set('hotel_timezone', $this->timezone);
            Setting::set('onboarding_completed', '1');

            // 2. Create the first room type
            $type = RoomType::create([
                'hotel_id' => $hotelId,
                'name' => $this->room_type_name,
                'base_price' => $this->base_price,
                'base_occupancy' => $this->base_occupancy,
            ]);

            // 3. Add the first room number
            Room::create([
                'hotel_id' => $hotelId,
                'room_type_id' => $type->id,
                'room_number' => $this->room_number,
                'floor' => $this->floor,
                'status' => 'available',
            ]);

            // 4. Log completion activity
            ActivityLog::create([
                'hotel_id' => $hotelId,
                'action' => 'Onboarding Complete',
                'description' => "Initial onboarding setup completed. Configured hotel: '{$this->hotel_name}' (First Room: {$this->room_number}).",
                'ip_address' => request()->ip(),
                'user_id' => Auth::id(),
            ]);
        });

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Hotel onboarded successfully!"
        ]);

        redirect()->to('/dashboard');
    }

    public function render(): mixed
    {
        return $this->view()->layout('layouts.guest');
    }
};
