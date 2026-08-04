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
use Illuminate\Support\Facades\Log;
use App\Mail\BookingRequested;
use App\Services\NotificationService;

new class extends Component
{
    public $hotel_id;
    public $checkin_date;
    public $checkout_date;
    public $guests_count = 1;
    public $step = 1; // 1: Search/Available Rooms, 2: Checkout Form & Stripe Simulation, 3: Success Screen

    public $selectedRoomTypeId;
    public $selectedRoomId;
    
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
    public $booking_status = 'Confirmed';
    public $booking_date = '';
    public $payment_status = 'Paid';
    public $payment_method = 'Card';

    // Dynamic Payment Fields
    public $card_number = '';
    public $card_holder = '';
    public $card_expiry = '';
    public $card_cvv = '';
    public $upi_id = '';
    public $cash_received = null;
    public $balance_change = 0;

    public function updatedCashReceived(): void
    {
        $received = (float) $this->cash_received;
        $this->balance_change = max(0, $received - $this->total_price);
    }

    public function mount($hotel_id = null, $city = null, $slug = null): void
    {
        $hotelsList = Hotel::where('status', 'approved')->get();
        
        $param = $slug ?: request()->query('hotel_id', $hotel_id);
        if ($param) {
            $parts = explode('-', (string)$param);
            $id = end($parts);
            $this->hotel_id = is_numeric($id) ? (int)$id : $param;
        } elseif ($hotelsList->count() > 0) {
            $this->hotel_id = $hotelsList->first()->id;
        }

        $this->checkin_date = request()->query('checkin', date('Y-m-d'));
        $this->checkout_date = request()->query('checkout', date('Y-m-d', strtotime('+1 day')));
        $this->guests_count = (int) request()->query('guests', 1);

        $roomId = request()->query('room_id');
        $roomTypeId = request()->query('room_type_id');

        if ($roomId) {
            $this->selectRoom((int)$roomId);
            return;
        }

        if ($roomTypeId) {
            $this->selectRoomType((int)$roomTypeId);
            return;
        }

        $this->calculatePriceAndDates();
    }

    public function updatedHotelId(): void
    {
        $this->selectedRoomId = null;
        $this->selectedRoomTypeId = null;
        $this->calculatePriceAndDates();
    }

    public function updatedCheckinDate(): void
    {
        $this->calculatePriceAndDates();
    }

    public function updatedCheckoutDate(): void
    {
        $this->calculatePriceAndDates();
    }

    public function updatedGuestsCount(): void
    {
        $this->calculatePriceAndDates();
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['checkin_date', 'checkout_date', 'guests_count', 'hotel_id', 'selectedRoomId', 'selectedRoomTypeId'])) {
            $this->calculatePriceAndDates();
        }
    }

    public function calculatePriceAndDates(): void
    {
        try {
            $checkin = \Illuminate\Support\Carbon::parse($this->checkin_date)->startOfDay();
            $checkout = \Illuminate\Support\Carbon::parse($this->checkout_date)->startOfDay();

            if ($checkout->lessThanOrEqualTo($checkin)) {
                $checkout = (clone $checkin)->addDay();
            }

            $this->checkin_date = $checkin->format('Y-m-d');
            $this->checkout_date = $checkout->format('Y-m-d');

            $days = $checkin->diffInDays($checkout);
            $this->total_days = max(1, (int) $days);
        } catch (\Throwable $e) {
            Log::error('Date calculation error in Booking Engine: ' . $e->getMessage());
            $this->total_days = 1;
        }

        if (!$this->selectedRoomId && !$this->selectedRoomTypeId && $this->hotel_id) {
            $firstRoom = Room::where('hotel_id', $this->hotel_id)->first();
            if ($firstRoom) {
                $this->selectedRoomId = $firstRoom->id;
                $this->selectedRoomTypeId = $firstRoom->room_type_id;
            }
        }

        $room = $this->selectedRoom;
        $roomType = $this->selectedRoomType;

        if ($room) {
            $rate = (float) ($room->price ?: ($roomType?->base_price ?: 2500));
            $this->total_price = round($rate * $this->total_days, 2);
            $this->checkRoomAvailability();
        } elseif ($roomType) {
            $rate = (float) ($roomType->daily_rate ?: ($roomType->base_price ?: 2500));
            $this->total_price = round($rate * $this->total_days, 2);
            $this->checkRoomAvailability();
        } else {
            $this->total_price = round(2500 * $this->total_days, 2);
            $this->is_available = true;
            $this->availability_message = 'Available for instant booking!';
        }
    }

    public function checkRoomAvailability(): void
    {
        $room = $this->selectedRoom;
        $roomType = $this->selectedRoomType;

        if (!$room && !$roomType) {
            $this->is_available = true;
            $this->availability_message = '';
            return;
        }

        if ($room) {
            $isOccupied = Reservation::whereHas('rooms', function ($q) use ($room) {
                $q->where('rooms.id', $room->id);
            })
            ->whereIn('status', ['Confirmed', 'Checked-In', 'Pending'])
            ->where('check_in_date', '<', $this->checkout_date)
            ->where('check_out_date', '>', $this->checkin_date)
            ->exists();

            if ($isOccupied || $room->status === 'Maintenance') {
                $this->is_available = false;
                $this->availability_message = 'This room is not available for the selected dates.';
            } else {
                $this->is_available = true;
                $this->availability_message = 'Available for instant booking!';
            }
        } elseif ($roomType) {
            $availableRoom = Room::where('room_type_id', $this->selectedRoomTypeId)
                ->where('hotel_id', $this->hotel_id)
                ->availableBetween($this->checkin_date, $this->checkout_date)
                ->first();

            if ($availableRoom) {
                $this->selectedRoomId = $availableRoom->id;
                $this->is_available = true;
                $this->availability_message = 'Available for instant booking!';
            } else {
                $this->is_available = false;
                $this->availability_message = 'No available rooms of this type for selected dates.';
            }
        }
    }

    public function getCurrencySymbolProperty(): string
    {
        $hotel = $this->selectedHotel;
        $curr = $hotel ? ($hotel->currency ?: 'USD') : 'USD';
        return match (strtoupper($curr)) {
            'INR', 'RS', '₹' => '₹',
            'EUR', '€' => '€',
            'GBP', '£' => '£',
            default => 'USD',
        };
    }

    public function getSelectedHotelProperty()
    {
        if (!$this->hotel_id) return null;
        return Hotel::with('images')->find($this->hotel_id);
    }

    public function getHotelsProperty()
    {
        return Hotel::where('status', 'approved')->get();
    }

    public function getSelectedRoomProperty()
    {
        if (!$this->selectedRoomId) return null;
        return Room::with('roomType')->find($this->selectedRoomId);
    }

    public function getSelectedRoomTypeProperty()
    {
        if ($this->selectedRoom) {
            return $this->selectedRoom->roomType;
        }
        if (!$this->selectedRoomTypeId) return null;
        return RoomType::find($this->selectedRoomTypeId);
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
        $this->selectedRoomId = (int)$roomId;
        $room = Room::with('roomType')->find($roomId);
        if ($room) {
            $this->selectedRoomTypeId = $room->room_type_id;
            $this->hotel_id = $room->hotel_id;
        }

        $this->calculatePriceAndDates();
        $this->step = 2;
    }

    public function selectRoomType($id): void
    {
        $this->selectedRoomTypeId = (int)$id;
        
        $availableRoom = Room::where('room_type_id', $id)
            ->where('hotel_id', $this->hotel_id)
            ->availableBetween($this->checkin_date, $this->checkout_date)
            ->first();

        if ($availableRoom) {
            $this->selectedRoomId = $availableRoom->id;
        } else {
            $fallbackRoom = Room::where('room_type_id', $id)->where('hotel_id', $this->hotel_id)->first();
            if ($fallbackRoom) {
                $this->selectedRoomId = $fallbackRoom->id;
            }
        }

        $this->calculatePriceAndDates();
        $this->step = 2;
    }

    public function processBooking(): void
    {
        $room = $this->selectedRoom;
        $roomType = $this->selectedRoomType;
        $hotel = $this->selectedHotel;

        if (!$this->hotel_id) {
            $this->hotel_id = $room?->hotel_id ?: ($roomType?->hotel_id ?: ($this->hotels->first()?->id));
        }

        // Auto-fill fallback values if guest info not entered to ensure instant 1-click booking
        if (empty(trim($this->guest_name ?? ''))) {
            $this->guest_name = 'Guest User';
        }
        if (empty(trim($this->guest_email ?? ''))) {
            $this->guest_email = 'guest@example.com';
        }
        if (empty(trim($this->guest_phone ?? ''))) {
            $this->guest_phone = '+91 9876543210';
        }
        if (empty(trim($this->guest_nationality ?? ''))) {
            $this->guest_nationality = 'Indian';
        }

        $this->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_nationality' => 'nullable|string',
        ]);

        $this->calculatePriceAndDates();

        if (!$this->is_available) {
            $this->addError('booking', 'Sorry, this room is not available for the selected dates. Please select another date or room.');
            return;
        }

        try {
            DB::transaction(function () use (&$room, &$roomType, &$hotel) {
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
            if (!$room && $this->selectedRoomId) {
                $room = Room::with('roomType')->find($this->selectedRoomId);
            }

            if (!$room && $this->selectedRoomTypeId) {
                $room = Room::where('room_type_id', $this->selectedRoomTypeId)
                    ->where('hotel_id', $this->hotel_id)
                    ->availableBetween($this->checkin_date, $this->checkout_date)
                    ->first();
            }

            if (!$room && $this->selectedRoomTypeId) {
                $room = Room::where('room_type_id', $this->selectedRoomTypeId)
                    ->where('hotel_id', $this->hotel_id)
                    ->first();
            }

            if (!$room) {
                $room = Room::where('hotel_id', $this->hotel_id)->first();
            }

            // 3. Create reservation record with Confirmed status for instant booking
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
                'pnr' => strtoupper(Str::random(6)),
            ]);

            // Link room and update room status to Occupied
            if ($room) {
                $roomPrice = $room->price ?: ($roomType?->base_price ?? 2500);
                $reservation->rooms()->attach($room->id, ['price' => $roomPrice]);
                $room->update(['status' => 'Occupied']);
                $this->selectedRoomId = $room->id;
            }

            // Map payment type to valid enum value ['Cash', 'Card', 'UPI']
            $paymentType = match ($this->payment_method) {
                'Card', 'Net Banking' => 'Card',
                'UPI' => 'UPI',
                default => 'Cash',
            };

            // 4. Create Payment record
            Payment::create([
                'hotel_id' => $this->hotel_id,
                'reservation_id' => $reservation->id,
                'amount' => max(1, $this->total_price),
                'payment_type' => $paymentType,
                'paid_at' => now(),
            ]);

            // 5. Log direct booking activity in ActivityLog
            ActivityLog::create([
                'hotel_id' => $this->hotel_id,
                'action' => 'Direct Booking',
                'description' => "Direct booking completed via Booking Engine for Guest: {$this->guest_name} (Room: " . ($room ? $room->room_number : 'N/A') . ")",
                'ip_address' => request()->ip(),
            ]);

            // 6. Send Hotel Owner/Admin notification
            try {
                NotificationService::notifyHotel(
                    (int) $this->hotel_id,
                    'New Room Booking',
                    "Aapke hotel me ek nayi room booking ({$this->guest_name}) hui hai. Kripya booking details check karein.",
                    '/reservations'
                );
            } catch (\Throwable $e) {}

            $this->booking_number = 'RES-' . $reservation->id . '-' . date('Y');
            $this->pnr = $reservation->pnr;
            $this->booking_status = $reservation->status ?: 'Confirmed';
            $this->booking_date = $reservation->created_at ? $reservation->created_at->format('d M Y, h:i A') : date('d M Y, h:i A');
            $this->payment_status = ($this->payment_method === 'Cash') ? 'Pay at Hotel' : 'Paid Online';

            // 7. Send Booking Request Notification to Registered Hotel Email
            try {
                $recipientEmail = $hotel?->email ?: 'rikkisaini61@gmail.com';
                if ($recipientEmail) {
                    Mail::to($recipientEmail)->send(new BookingRequested($reservation));
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send booking request email: ' . $e->getMessage());
            }
        });

            $this->step = 3;
        } catch (\Throwable $e) {
            Log::error('Booking process failed: ' . $e->getMessage());
            $this->addError('booking', 'Booking process error: ' . $e->getMessage());
        }
    }

    public function render(): mixed
    {
        return $this->view()->layout('layouts.guest');
    }
};

