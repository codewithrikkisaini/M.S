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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingRequested;
use App\Services\NotificationService;

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
    public $selectedRoomId;
    public $selectedRoom;
    
    // Guest fields
    public $guest_name;
    public $guest_email;
    public $guest_phone;
    public $guest_nationality = 'Indian';

    // Price details
    public $total_days = 1;
    public $total_price = 0;
    public $is_available = true;
    public $availability_message = '';
    
    public $booking_number;
    public $pnr;
    public $payment_method = 'Card';

    public function mount($hotel_id = null): void
    {
        $this->hotels = Hotel::where('status', 'approved')->get();
        
        $requestedHotelId = request()->query('hotel_id', $hotel_id);
        if ($requestedHotelId) {
            $this->hotel_id = $requestedHotelId;
        } elseif ($this->hotels->count() > 0) {
            $this->hotel_id = $this->hotels->first()->id;
        }

        $this->checkin_date = request()->query('checkin', date('Y-m-d'));
        $this->checkout_date = request()->query('checkout', date('Y-m-d', strtotime('+1 day')));
        $this->guests_count = (int) request()->query('guests', 1);

        $this->calculatePriceAndDates();

        $roomId = request()->query('room_id');
        $roomTypeId = request()->query('room_type_id');

        if ($roomId) {
            $room = Room::with('roomType')->find($roomId);
            if ($room) {
                $this->selectRoom($room->id);
                return;
            }
        }

        if ($roomTypeId) {
            $roomType = RoomType::where('hotel_id', $this->hotel_id)->find($roomTypeId);
            if (!$roomType) {
                $roomType = RoomType::find($roomTypeId);
            }
            if ($roomType) {
                $this->selectRoomType($roomType->id);
            }
        }
    }

    public function updatedCheckinDate(): void
    {
        $this->calculatePriceAndDates();
    }

    public function updatedCheckoutDate(): void
    {
        $this->calculatePriceAndDates();
    }

    public function calculatePriceAndDates(): void
    {
        try {
            $checkin = new \DateTime($this->checkin_date);
            $checkout = new \DateTime($this->checkout_date);
            if ($checkout <= $checkin) {
                $checkout = (clone $checkin)->modify('+1 day');
                $this->checkout_date = $checkout->format('Y-m-d');
            }
            $diff = $checkin->diff($checkout);
            $this->total_days = max(1, (int) $diff->days);
        } catch (\Exception $e) {
            $this->total_days = 1;
        }

        if ($this->selectedRoom) {
            $rate = (float) ($this->selectedRoom->price ?: ($this->selectedRoomType->base_price ?? 59.95));
            $this->total_price = $rate * $this->total_days;
            $this->checkRoomAvailability();
        } elseif ($this->selectedRoomType) {
            $rate = (float) ($this->selectedRoomType->daily_rate ?: ($this->selectedRoomType->base_price ?: 59.95));
            $this->total_price = $rate * $this->total_days;
            $this->checkRoomAvailability();
        }
    }

    public function checkRoomAvailability(): void
    {
        if (!$this->selectedRoom && !$this->selectedRoomType) {
            $this->is_available = true;
            $this->availability_message = '';
            return;
        }

        if ($this->selectedRoom) {
            // Check if specific room is available for dates
            $isOccupied = Reservation::whereHas('rooms', function ($q) {
                $q->where('rooms.id', $this->selectedRoom->id);
            })
            ->whereIn('status', ['Confirmed', 'Checked-In', 'Pending'])
            ->where('check_in_date', '<', $this->checkout_date)
            ->where('check_out_date', '>', $this->checkin_date)
            ->exists();

            if ($isOccupied || $this->selectedRoom->status === 'Maintenance') {
                $this->is_available = false;
                $this->availability_message = 'This room is not available for the selected dates.';
            } else {
                $this->is_available = true;
                $this->availability_message = 'Available for instant booking!';
            }
        } elseif ($this->selectedRoomType) {
            $availableRoom = Room::where('room_type_id', $this->selectedRoomTypeId)
                ->where('hotel_id', $this->hotel_id)
                ->availableBetween($this->checkin_date, $this->checkout_date)
                ->first();

            if ($availableRoom) {
                $this->selectedRoom = $availableRoom;
                $this->selectedRoomId = $availableRoom->id;
                $this->is_available = true;
                $this->availability_message = 'Available for instant booking!';
            } else {
                $this->is_available = false;
                $this->availability_message = 'No available rooms of this type for selected dates.';
            }
        }
    }

    public function getSelectedHotelProperty()
    {
        if (!$this->hotel_id) return null;
        return Hotel::with('images')->find($this->hotel_id);
    }

    public function getRoomsProperty()
    {
        if (!$this->hotel_id) return collect();
        
        $rooms = Room::with('roomType')
            ->where('hotel_id', $this->hotel_id)
            ->get();

        foreach ($rooms as $room) {
            $isOccupied = Reservation::whereHas('rooms', function ($q) use ($room) {
                $q->where('rooms.id', $room->id);
            })
            ->whereIn('status', ['Confirmed', 'Checked-In', 'Pending'])
            ->where('check_in_date', '<', $this->checkout_date)
            ->where('check_out_date', '>', $this->checkin_date)
            ->exists();

            $room->is_available = !$isOccupied && $room->status !== 'Maintenance';
        }

        return $rooms;
    }

    public function getRoomTypesProperty()
    {
        if (!$this->hotel_id) return collect();
        return RoomType::where('hotel_id', $this->hotel_id)
            ->whereHas('rooms')
            ->get();
    }

    public function selectRoom($roomId): void
    {
        $this->selectedRoomId = $roomId;
        $this->selectedRoom = Room::with('roomType')->findOrFail($roomId);
        $this->selectedRoomTypeId = $this->selectedRoom->room_type_id;
        $this->selectedRoomType = $this->selectedRoom->roomType;
        $this->hotel_id = $this->selectedRoom->hotel_id;

        $this->calculatePriceAndDates();
        $this->step = 2;
    }

    public function selectRoomType($id): void
    {
        $this->selectedRoomTypeId = $id;
        $this->selectedRoomType = RoomType::findOrFail($id);
        
        $availableRoom = Room::where('room_type_id', $id)
            ->where('hotel_id', $this->hotel_id)
            ->availableBetween($this->checkin_date, $this->checkout_date)
            ->first();

        if ($availableRoom) {
            $this->selectedRoom = $availableRoom;
            $this->selectedRoomId = $availableRoom->id;
        } else {
            $this->selectedRoom = Room::where('room_type_id', $id)->where('hotel_id', $this->hotel_id)->first();
            if ($this->selectedRoom) {
                $this->selectedRoomId = $this->selectedRoom->id;
            }
        }

        $this->calculatePriceAndDates();
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

        $this->calculatePriceAndDates();

        if (!$this->is_available) {
            $this->addError('booking', 'Sorry, this room is not available for the selected dates. Please select another date or room.');
            return;
        }

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

            // 2. Allocate selected room or available room
            $room = $this->selectedRoom;
            if (!$room) {
                $room = Room::where('room_type_id', $this->selectedRoomTypeId)
                    ->where('hotel_id', $this->hotel_id)
                    ->availableBetween($this->checkin_date, $this->checkout_date)
                    ->first();
            }

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
                'status' => 'Pending',
                'pnr' => strtoupper(Str::random(6)),
            ]);

            // Link room and update room status
            if ($room) {
                $reservation->rooms()->attach($room->id, ['price' => $room->price ?: ($this->selectedRoomType->base_price ?? 2500)]);
                $room->update(['status' => 'Occupied']);
            }

            // 4. Create Payment record
            Payment::create([
                'hotel_id' => $this->hotel_id,
                'reservation_id' => $reservation->id,
                'amount' => $this->total_price,
                'payment_type' => $this->payment_method,
                'paid_at' => now(),
            ]);

            // 5. Log direct booking activity in ActivityLog
            ActivityLog::create([
                'hotel_id' => $this->hotel_id,
                'action' => 'Direct Booking',
                'description' => "Direct booking received via Booking Engine for Guest: {$this->guest_name} (Room: " . ($room ? $room->room_number : 'N/A') . ")",
                'ip_address' => request()->ip(),
            ]);

            // 6. Send Hotel Owner/Admin notification
            NotificationService::notifyHotel(
                (int) $this->hotel_id,
                'New Room Booking',
                "Aapke hotel me ek nayi room booking ({$this->guest_name}) hui hai. Kripya booking details check karein.",
                '/reservations'
            );

            $this->booking_number = 'RES-' . $reservation->id . '-' . date('Y');
            $this->pnr = $reservation->pnr;

            // 7. Send Booking Request Notification to Registered Hotel Email
            try {
                $hotel = Hotel::find($this->hotel_id);
                $recipientEmail = $hotel?->email ?: 'rikkisaini61@gmail.com';
                if ($recipientEmail) {
                    Mail::to($recipientEmail)->send(new BookingRequested($reservation));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send booking request email: ' . $e->getMessage());
            }
        });

        $this->step = 3;
    }

    public function render(): mixed
    {
        return $this->view()->layout('layouts.guest');
    }
};
