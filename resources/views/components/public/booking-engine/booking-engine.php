<?php

use Livewire\Component;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Invoice;
use App\Models\ActivityLog;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public $hotels;
    public $hotel_id;
    public $checkin_date;
    public $checkout_date;
    public $guests_count = 1;
    public $step = 1; // 1: Search/Available Rooms, 2: Checkout Form & Stripe Simulation, 3: Success Screen

    public $selectedRoomTypeId;
    public $selectedRoomType;
    
    // Guest fields
    public $guest_name;
    public $guest_email;
    public $guest_phone;
    public $guest_nationality = 'Indian';

    // Price details
    public $total_days = 1;
    public $total_price = 0;
    
    public $booking_number;

    public function mount($hotel_id = null): void
    {
        $this->hotels = Hotel::all();
        if ($hotel_id) {
            $this->hotel_id = $hotel_id;
        } elseif ($this->hotels->count() > 0) {
            $this->hotel_id = $this->hotels->first()->id;
        }
        $this->checkin_date = date('Y-m-d');
        $this->checkout_date = date('Y-m-d', strtotime('+1 day'));
    }

    public function getSelectedHotelProperty()
    {
        if (!$this->hotel_id) return null;
        return Hotel::with(['images' => function ($query) {
            $query->orderByDesc('is_primary')->orderBy('id');
        }])->find($this->hotel_id);
    }

    public function getRoomTypesProperty()
    {
        if (!$this->hotel_id) return collect();
        return RoomType::where('hotel_id', $this->hotel_id)->get();
    }

    public function selectRoomType($id): void
    {
        $this->selectedRoomTypeId = $id;
        $this->selectedRoomType = RoomType::findOrFail($id);
        
        $checkin = new DateTime($this->checkin_date);
        $checkout = new DateTime($this->checkout_date);
        $this->total_days = $checkout->diff($checkin)->days ?: 1;
        $this->total_price = $this->selectedRoomType->base_price * $this->total_days;
        
        $this->step = 2;
    }

    public function processBooking(): void
    {
        $this->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_nationality' => 'required|string',
        ]);

        DB::transaction(function () {
            // 1. Create or update the Guest globally
            $guest = Guest::where('email', $this->guest_email)->first();

            if (!$guest) {
                $guest = Guest::create([
                    'guest_id' => 'G-' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT),
                    'hotel_id' => $this->hotel_id,
                    'email' => $this->guest_email,
                    'name' => $this->guest_name,
                    'phone' => $this->guest_phone,
                    'nationality' => $this->guest_nationality,
                ]);
            } else {
                $guest->update([
                    'hotel_id' => $this->hotel_id,
                    'name' => $this->guest_name,
                    'phone' => $this->guest_phone,
                    'nationality' => $this->guest_nationality,
                ]);
            }

            // 2. Allocate an available room of this type
            $room = Room::where('room_type_id', $this->selectedRoomTypeId)
                ->where('hotel_id', $this->hotel_id)
                ->where('status', 'Available')
                ->first();

            // Fallback: If no vacant room is found, pick any room under this type for demo
            if (!$room) {
                $room = Room::where('room_type_id', $this->selectedRoomTypeId)
                    ->where('hotel_id', $this->hotel_id)
                    ->first();
            }

            // 3. Create reservation record
            $reservation = Reservation::create([
                'hotel_id' => $this->hotel_id,
                'guest_id' => $guest->id,
                'check_in_date' => $this->checkin_date,
                'check_out_date' => $this->checkout_date,
                'adults' => $this->guests_count ?: 1,
                'children' => 0,
                'discount_type' => 'Fixed',
                'discount_value' => 0,
                'tax_rate' => 18,
                'status' => 'Confirmed',
            ]);

            // Link room and update room status
            if ($room) {
                $reservation->rooms()->attach($room->id, ['price' => $room->price]);
                $room->update(['status' => 'Occupied']);
            }

            // 4. Create Payment record (mocking Stripe credit card gateway)
            Payment::create([
                'hotel_id' => $this->hotel_id,
                'reservation_id' => $reservation->id,
                'amount' => $this->total_price,
                'payment_type' => 'Card',
                'paid_at' => now(),
            ]);

            // 5. Log direct booking activity in ActivityLog
            ActivityLog::create([
                'hotel_id' => $this->hotel_id,
                'action' => 'Direct Booking',
                'description' => "Direct booking received via Booking Engine for Guest: {$this->guest_name} (Room: " . ($room ? $room->room_number : 'N/A') . ")",
                'ip_address' => request()->ip(),
            ]);

            $this->booking_number = 'RES-' . $reservation->id . '-' . date('Y');
        });

        $this->step = 3;
    }

    public function render(): mixed
    {
        return $this->view()->layout('layouts.guest');
    }
};
