<?php

use Livewire\Component;
use App\Models\Room;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Payment;
use App\Services\ReservationService;
use Carbon\Carbon;

new class extends Component
{
    public string $startDate = '';
    public string $activeView = 'timeline'; // 'timeline' or 'month'
    
    // Modal properties
    public bool $showModal = false;
    public int $modalRoomId = 0;
    public string $modalRoomNumber = '';
    public string $modalCheckInDate = '';
    public string $modalCheckOutDate = '';
    
    public bool $modalIsNewGuest = false;
    public string $modalGuestId = '';
    public string $modalNewGuestName = '';
    public string $modalNewGuestEmail = '';
    public string $modalNewGuestPhone = '';
    
    public int $modalAdults = 1;
    public int $modalChildren = 0;
    public string $modalSpecialNotes = '';
    public string $modalPaymentType = 'Cash';
    public string $modalPaymentAmount = '';

    public function mount(): void
    {
        $this->startDate = Carbon::today()->format('Y-m-d');
    }

    public function navigate(int $days): void
    {
        $this->startDate = Carbon::parse($this->startDate)->addDays($days)->format('Y-m-d');
    }

    public function setToday(): void
    {
        $this->startDate = Carbon::today()->format('Y-m-d');
    }

    public function switchView(string $view): void
    {
        $this->activeView = $view;
        if ($view === 'month') {
            // Dispatch event to re-render fullcalendar after UI switches
            $this->dispatch('refresh-fullcalendar');
        }
    }

    public function openBookingModal(int $roomId, string $date): void
    {
        if ($roomId > 0) {
            $room = Room::find($roomId);
            if (!$room) return;
            $this->modalRoomId = $room->id;
            $this->modalRoomNumber = $room->room_number;
        } else {
            $this->modalRoomId = 0;
            $this->modalRoomNumber = '';
        }

        $this->modalCheckInDate = $date;
        $this->modalCheckOutDate = Carbon::parse($date)->addDay()->format('Y-m-d');
        
        // Reset guest inputs
        $this->modalIsNewGuest = false;
        $this->modalGuestId = '';
        $this->modalNewGuestName = '';
        $this->modalNewGuestEmail = '';
        $this->modalNewGuestPhone = '';
        $this->modalAdults = 1;
        $this->modalChildren = 0;
        $this->modalSpecialNotes = '';
        $this->modalPaymentType = 'Cash';
        $this->modalPaymentAmount = '';

        $this->showModal = true;
    }

    public function closeBookingModal(): void
    {
        $this->showModal = false;
    }

    public function saveBooking(ReservationService $service): void
    {
        $rules = [
            'modalRoomId'        => 'required|exists:rooms,id',
            'modalCheckInDate'   => 'required|date',
            'modalCheckOutDate'  => 'required|date|after:modalCheckInDate',
            'modalAdults'        => 'required|integer|min:1',
            'modalChildren'      => 'required|integer|min:0',
            'modalPaymentType'   => 'required|in:Cash,Card,UPI',
            'modalPaymentAmount' => 'nullable|numeric|min:0',
        ];

        if ($this->modalIsNewGuest) {
            $rules['modalNewGuestName'] = 'required|string|max:255';
            $rules['modalNewGuestEmail'] = 'nullable|email|unique:guests,email';
            $rules['modalNewGuestPhone'] = 'nullable|string|max:20';
        } else {
            $rules['modalGuestId'] = 'required|exists:guests,id';
        }

        $this->validate($rules);

        // Check availability
        $available = Room::availableBetween($this->modalCheckInDate, $this->modalCheckOutDate)
            ->where('id', $this->modalRoomId)
            ->exists();

        if (!$available) {
            $this->addError('modalRoomId', 'This room is not available for the selected dates.');
            return;
        }

        // Create guest if new
        $guestId = $this->modalGuestId;
        if ($this->modalIsNewGuest) {
            $guest_id_str = 'G-' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT);
            while (Guest::where('guest_id', $guest_id_str)->exists()) {
                $guest_id_str = 'G-' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT);
            }
            $guest = Guest::create([
                'guest_id' => $guest_id_str,
                'name'     => $this->modalNewGuestName,
                'email'    => $this->modalNewGuestEmail ?: null,
                'phone'    => $this->modalNewGuestPhone ?: null,
            ]);
            $guestId = (string)$guest->id;
        }

        // Save reservation
        $reservation = $service->saveReservation(null, [
            'guest_id'       => $guestId,
            'room_ids'       => [$this->modalRoomId],
            'check_in_date'  => $this->modalCheckInDate,
            'check_out_date' => $this->modalCheckOutDate,
            'adults'         => $this->modalAdults,
            'children'       => $this->modalChildren,
            'discount_type'  => 'Fixed',
            'discount_value' => 0,
            'tax_rate'       => 18,
            'special_notes'  => $this->modalSpecialNotes,
            'status'         => 'Confirmed',
        ], false);

        // Add payment if provided
        if ($this->modalPaymentAmount !== '' && (float) $this->modalPaymentAmount > 0) {
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount'         => $this->modalPaymentAmount,
                'payment_type'   => $this->modalPaymentType,
                'paid_at'        => now(),
            ]);
        }

        $this->showModal = false;
        $this->dispatch('toast', ['message' => 'Reservation created successfully!', 'type' => 'success']);
    }

    public function render(): mixed
    {
        $start = Carbon::parse($this->startDate);
        $days = [];
        for ($i = 0; $i < 14; $i++) {
            $date = $start->copy()->addDays($i);
            $days[] = [
                'date_str' => $date->format('Y-m-d'),
                'day_name' => $date->format('D'),
                'day_num'  => $date->format('d'),
                'is_today' => $date->isToday(),
            ];
        }

        $end_str = $start->copy()->addDays(13)->format('Y-m-d');
        $start_str = $this->startDate;

        $rooms = Room::with(['roomType'])->orderBy('room_number')->get();

        // Get reservations overlapping with timeline range
        $reservations = Reservation::with(['guest', 'rooms'])
            ->where('status', '!=', 'Cancelled')
            ->where(function ($query) use ($start_str, $end_str) {
                $query->where(function ($q) use ($start_str, $end_str) {
                    $q->where('check_in_date', '<=', $end_str)
                      ->where('check_out_date', '>=', $start_str);
                });
            })
            ->get();

        // Map reservations by room_id and date for fast lookup
        $roomBookings = [];
        foreach ($reservations as $res) {
            foreach ($res->rooms as $room) {
                $resStart = Carbon::parse($res->check_in_date);
                $resEnd = Carbon::parse($res->check_out_date);
                
                $temp = $resStart->copy();
                while ($temp->lt($resEnd)) {
                    $dateStr = $temp->format('Y-m-d');
                    $roomBookings[$room->id][$dateStr] = [
                        'reservation_id' => $res->id,
                        'guest_name'     => optional($res->guest)->name ?? 'Guest',
                        'status'         => $res->status,
                        'is_check_in'    => $dateStr === $res->check_in_date,
                    ];
                    $temp->addDay();
                }
            }
        }

        $guests = Guest::orderBy('name')->get();

        // Prepare format for FullCalendar events
        $events = [];
        foreach ($reservations as $res) {
            foreach ($res->rooms as $room) {
                $events[] = [
                    'id'    => $res->id,
                    'title' => 'Room ' . $room->room_number . ' - ' . (optional($res->guest)->name ?? 'Guest'),
                    'start' => $res->check_in_date,
                    'end'   => Carbon::parse($res->check_out_date)->addDay()->format('Y-m-d'), // FullCalendar needs end date exclusive + 1
                    'extendedProps' => [
                        'guest'  => optional($res->guest)->name ?? 'N/A',
                        'room'   => $room->room_number,
                        'status' => $res->status,
                    ],
                ];
            }
        }

        return $this->view([
            'days'         => $days,
            'rooms'        => $rooms,
            'roomBookings' => $roomBookings,
            'guests'       => $guests,
            'events'       => $events,
        ]);
    }
}
