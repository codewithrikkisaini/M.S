<?php

use Livewire\Component;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Payment;
use App\Services\ReservationService;

new class extends Component
{
    public string $guest_id = '', $check_in_date = '', $check_out_date = '';
    public array $room_ids = [];
    public int $adults = 1, $children = 0;
    public string $special_notes = '';
    public string $discount_type = 'Fixed', $discount_value = '0';
    public string $tax_rate = '18';

    public string $payment_type = 'Cash', $payment_amount = '';
    public string $misc_charge = '0';
    public string $pricing_mode = 'auto';

    // New guest fields
    public bool $is_new_guest = false;
    public string $new_guest_name = '', $new_guest_email = '', $new_guest_phone = '';

    public function mount(): void
    {
        $this->check_in_date = request()->query('check_in_date', '');
        $this->check_out_date = request()->query('check_out_date', '');
        $roomId = request()->query('room_id', '');
        if ($roomId) {
            $this->room_ids = [(int)$roomId];
        }
    }

    public function updatedCheckInDate(): void { $this->room_ids = []; }

    public function updatedCheckOutDate(): void { $this->room_ids = []; }

    public function updatedRoomIds(): void
    {
        if (!empty($this->room_ids)) {
            $room = Room::with('roomType')->whereIn('id', $this->room_ids)->first();
            if ($room && $room->roomType) {
                $this->tax_rate = (string) ($room->roomType->tax_percentage ?? 15);
            }
        }
    }

    public function save(ReservationService $service): void
    {
        $rules = [
            'room_ids'        => 'required|array|min:1',
            'room_ids.*'      => 'integer|exists:rooms,id',
            'check_in_date'   => 'required|date',
            'check_out_date'  => 'required|date|after:check_in_date',
            'adults'          => 'required|integer|min:1',
            'children'        => 'required|integer|min:0',
            'discount_type'   => 'required|in:Fixed,Percentage',
            'discount_value'  => 'nullable|numeric|min:0',
            'tax_rate'        => 'required|numeric|min:0|max:100',
            'pricing_mode'    => 'required|in:auto,daily,weekly,monthly',
            'payment_type'    => 'required|in:Cash,Card,UPI',
            'payment_amount'  => 'nullable|numeric|min:0',
            'misc_charge'     => 'nullable|numeric|min:0',
        ];

        if ($this->is_new_guest) {
            $rules['new_guest_name'] = 'required|string|max:255';
            $rules['new_guest_email'] = 'nullable|email|unique:guests,email';
            $rules['new_guest_phone'] = 'nullable|string|max:20';
        } else {
            $rules['guest_id'] = 'required|exists:guests,id';
        }

        $this->validate($rules);

        if ($this->is_new_guest) {
            $guest_id_str = 'G-' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT);
            while (Guest::where('guest_id', $guest_id_str)->exists()) {
                $guest_id_str = 'G-' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT);
            }
            $guest = Guest::create([
                'guest_id' => $guest_id_str,
                'name'     => $this->new_guest_name,
                'email'    => $this->new_guest_email ?: null,
                'phone'    => $this->new_guest_phone ?: null,
            ]);
            $this->guest_id = (string)$guest->id;
        }

        foreach ($this->room_ids as $roomId) {
            $available = Room::availableBetween($this->check_in_date, $this->check_out_date)
                ->where('id', $roomId)
                ->exists();

            if (!$available) {
                $this->addError('room_ids', 'One of the selected rooms is not available for these dates.');
                return;
            }
        }


        $reservation = $service->saveReservation(null, [
            'guest_id'       => $this->guest_id,
            'room_ids'       => $this->room_ids,
            'check_in_date'  => $this->check_in_date,
            'check_out_date' => $this->check_out_date,
            'adults'         => $this->adults,
            'children'       => $this->children,
            'discount_type'  => $this->discount_type,
            'discount_value' => $this->discount_value !== '' ? $this->discount_value : 0,
            'tax_rate'       => $this->tax_rate !== '' ? $this->tax_rate : 18,
            'pricing_mode'   => $this->pricing_mode,
            'special_notes'  => $this->special_notes,
            'misc_charge'    => $this->misc_charge !== '' ? $this->misc_charge : 0,
            'status'         => 'Confirmed',
        ], false);

        if ($this->payment_amount !== '' && (float) $this->payment_amount > 0) {
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount'         => $this->payment_amount,
                'payment_type'   => $this->payment_type,
                'paid_at'        => now(),
            ]);
        }

        session()->flash('toast', ['message' => 'Reservation created successfully!', 'type' => 'success']);
        $this->redirect(route('reservations.index'), navigate: true);
    }

    public function render(): mixed
    {
        $guests = Guest::orderBy('name')->get();

        $rooms = collect();
        if ($this->check_in_date && $this->check_out_date) {
            $rooms = Room::with(['latestHousekeeping', 'activeMaintenanceTickets', 'roomType'])
                ->availableBetween($this->check_in_date, $this->check_out_date)
                ->orderBy('room_number')
                ->get();
        }

        $charges = null;
        $balanceDue = 0;

        if (!empty($this->room_ids) && $this->check_in_date && $this->check_out_date) {
            $preview = new Reservation([
                'check_in_date'  => $this->check_in_date,
                'check_out_date' => $this->check_out_date,
                'discount_type'  => $this->discount_type,
                'discount_value' => $this->discount_value !== '' ? $this->discount_value : 0,
                'tax_rate'       => $this->tax_rate !== '' ? $this->tax_rate : 18,
                'pricing_mode'   => $this->pricing_mode,
            ]);
            $preview->setRelation('rooms', Room::with('roomType')->whereIn('id', $this->room_ids)->get());
            $preview->misc_charge = $this->misc_charge !== '' ? $this->misc_charge : 0;
            $charges = $preview->calculateCharges(null, $this->pricing_mode);
            $balanceDue = round($charges['total'] - (float) ($this->payment_amount !== '' ? $this->payment_amount : 0), 2);
        }

        return $this->view(compact('guests', 'rooms', 'charges', 'balanceDue'));
    }
};
