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
        $this->hotels = Hotel::where('status', 'approved')->get();
        if ($hotel_id) {
            $this->hotel_id = $hotel_id;
        } elseif ($this->hotels->count() > 0) {
            $this->hotel_id = $this->hotels->first()->id;
        }
        $this->checkin_date = date('Y-m-d');
        $this->checkout_date = date('Y-m-d', strtotime('+1 day'));
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
            // 1. Create or update the Guest
            $guest = Guest::updateOrCreate(
                ['email' => $this->guest_email, 'hotel_id' => $this->hotel_id],
                [
                    'name' => $this->guest_name,
                    'phone' => $this->guest_phone,
                    'nationality' => $this->guest_nationality,
                ]
            );

            // 2. Allocate an available room of this type
            $room = Room::where('room_type_id', $this->selectedRoomTypeId)
                ->where('hotel_id', $this->hotel_id)
                ->where('status', 'available')
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
                'room_id' => $room ? $room->id : null,
                'check_in' => $this->checkin_date,
                'check_out' => $this->checkout_date,
                'status' => 'confirmed',
                'total_price' => $this->total_price,
            ]);

            // 4. Create invoice
            $invoice = Invoice::create([
                'hotel_id' => $this->hotel_id,
                'reservation_id' => $reservation->id,
                'invoice_number' => 'INV-DIRECT-' . date('Ymd') . '-' . rand(1000, 9999),
                'amount' => $this->total_price,
                'status' => 'paid',
            ]);

            // 5. Create Payment record (mocking Stripe credit card gateway)
            Payment::create([
                'hotel_id' => $this->hotel_id,
                'invoice_id' => $invoice->id,
                'amount' => $this->total_price,
                'payment_method' => 'Stripe Checkout',
                'transaction_id' => 'ch_stripe_' . str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'),
                'payment_date' => now(),
            ]);

            // Update room status
            if ($room) {
                $room->update(['status' => 'occupied']);
            }

            // 6. Log direct booking activity in ActivityLog
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
