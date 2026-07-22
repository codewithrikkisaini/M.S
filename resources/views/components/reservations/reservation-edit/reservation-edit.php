<?php

use Livewire\Component;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Payment;
use App\Services\ReservationService;

new class extends Component
{
    public Reservation $reservation;

    public string $guest_id = '', $check_in_date = '', $check_out_date = '';
    public array $room_ids = [];
    public int $adults = 1, $children = 0;
    public string $special_notes = '', $status = 'Confirmed';
    public string $discount_type = 'Fixed', $discount_value = '0';
    public string $tax_rate = '18';
    public string $misc_charge = '0';
    public string $pricing_mode = 'auto';

    public string $payment_type = 'Cash', $payment_amount = '';

    public function mount(Reservation $reservation): void
    {
        $reservation->load('rooms');

        $this->reservation    = $reservation;
        $this->guest_id       = (string) $reservation->guest_id;
        $this->room_ids       = $reservation->rooms->pluck('id')->all();
        $this->check_in_date  = $reservation->check_in_date;
        $this->check_out_date = $reservation->check_out_date;
        $this->adults         = $reservation->adults;
        $this->children       = $reservation->children;
        $this->discount_type  = $reservation->discount_type;
        $this->discount_value = (string) $reservation->discount_value;
        $this->tax_rate       = (string) $reservation->tax_rate;
        $this->pricing_mode   = $reservation->pricing_mode ?? 'auto';
        $this->misc_charge    = (string) ($reservation->misc_charge ?? 0);
        $this->special_notes  = $reservation->special_notes ?? '';
        $this->status         = $reservation->status;
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
        $this->validate([
            'guest_id'        => 'required|exists:guests,id',
            'room_ids'        => 'required|array|min:1',
            'room_ids.*'      => 'integer|exists:rooms,id',
            'check_in_date'   => 'required|date',
            'check_out_date'  => 'required|date|after:check_in_date',
            'adults'          => 'required|integer|min:1',
            'children'        => 'required|integer|min:0',
            'status'          => 'required|in:Confirmed,Checked-In,Checked-Out,Cancelled',
            'discount_type'   => 'required|in:Fixed,Percentage',
            'discount_value'  => 'nullable|numeric|min:0',
            'tax_rate'        => 'required|numeric|min:0|max:100',
            'misc_charge'     => 'nullable|numeric|min:0',
        ]);

        foreach ($this->room_ids as $roomId) {
            $available = Room::availableBetween($this->check_in_date, $this->check_out_date, $this->reservation->id)
                ->where('id', $roomId)
                ->exists();

            if (!$available) {
                $this->addError('room_ids', 'One of the selected rooms is not available for these dates.');
                return;
            }
        }

        // Checked-In / Checked-Out are operational states with side effects (room status,
        // payment gate, invoice creation) — they must only be reached via the Check-In/Check-Out
        // actions on the list page, never set directly through this form.
        $allowedTransitions = [
            'Confirmed'   => ['Confirmed', 'Cancelled'],
            'Cancelled'   => ['Cancelled', 'Confirmed'],
            'Checked-In'  => ['Checked-In'],
            'Checked-Out' => ['Checked-Out'],
        ];
        $currentStatus = $this->reservation->status;

        if (!in_array($this->status, $allowedTransitions[$currentStatus] ?? [$currentStatus])) {
            $this->addError('status', 'Use the Check-In / Check-Out actions to change this status.');
            return;
        }

        $service->saveReservation($this->reservation->id, [
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
            'status'         => $this->status,
            'misc_charge'    => $this->misc_charge !== '' ? $this->misc_charge : 0,
        ], true);

        session()->flash('toast', ['message' => 'Reservation updated successfully!', 'type' => 'success']);
        $this->redirect(route('reservations.index'), navigate: true);
    }

    public function addPayment(): void
    {
        $this->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_type'   => 'required|in:Cash,Card,UPI',
        ]);

        Payment::create([
            'reservation_id' => $this->reservation->id,
            'amount'         => $this->payment_amount,
            'payment_type'   => $this->payment_type,
            'paid_at'        => now(),
        ]);

        $this->payment_amount = '';
        $this->dispatch('toast', message: 'Payment recorded successfully!', type: 'success');
    }

    public function render(): mixed
    {
        $guests = Guest::orderBy('name')->get();

        $rooms = collect();
        if ($this->check_in_date && $this->check_out_date) {
            $rooms = Room::with(['latestHousekeeping', 'activeMaintenanceTickets', 'roomType'])
                ->availableBetween($this->check_in_date, $this->check_out_date, $this->reservation->id)
                ->orderBy('room_number')
                ->get();
        }

        $current = Reservation::with(['rooms', 'payments'])->find($this->reservation->id);
        $payments = $current->payments->sortByDesc('paid_at')->values();
        $totalPaid = $current->total_paid;
        
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
            $balanceDue = round($charges['total'] - $totalPaid, 2);
        }

        return $this->view(compact('guests', 'rooms', 'payments', 'charges', 'totalPaid', 'balanceDue'));
    }
};
