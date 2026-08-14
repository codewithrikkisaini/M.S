<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Payment;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

new class extends Component
{
    use WithFileUploads;

    public Reservation $reservation;

    public string $guest_id = '', $check_in_date = '', $check_out_date = '';
    public array $room_ids = [];
    public int $adults = 1, $children = 0;
    public string $special_notes = '', $status = 'Confirmed';
    public string $discount_type = 'Fixed', $discount_value = '0';
    public string $tax_rate = '18';
    public string $booking_type = 'Walk in';

    public string $id_type = '';
    public string $guest_id_number = '';

    // ID Cards & Guest Photo File Uploads & Base64 Camera Captures
    public $id_card_front;
    public $id_card_back;
    public $guest_photo;
    public string $id_card_front_base64 = '';
    public string $id_card_back_base64 = '';
    public string $guest_photo_base64 = '';
    public ?string $existing_id_card_front = null;
    public ?string $existing_id_card_back = null;
    public ?string $existing_guest_photo = null;

    public string $payment_type = 'Cash', $payment_amount = '';

    public function mount(Reservation $reservation): void
    {
        $reservation->load(['rooms', 'guest']);

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
        $this->special_notes  = $reservation->special_notes ?? '';
        $this->status         = $reservation->status;
        $this->booking_type   = $reservation->booking_type ?? 'Walk in';

        if ($reservation->guest) {
            $this->id_type                 = $reservation->guest->id_type ?? '';
            $this->guest_id_number         = $reservation->guest->id_number ?? $reservation->guest->passport_number ?? '';
            $this->existing_id_card_front  = $reservation->guest->id_card_front;
            $this->existing_id_card_back   = $reservation->guest->id_card_back;
            $this->existing_guest_photo    = $reservation->guest->guest_photo;
        }
    }

    public function updatedGuestId($value): void
    {
        if ($value) {
            $guest = Guest::find($value);
            if ($guest) {
                $this->id_type                 = $guest->id_type ?? '';
                $this->guest_id_number         = $guest->id_number ?? $guest->passport_number ?? '';
                $this->existing_id_card_front  = $guest->id_card_front;
                $this->existing_id_card_back   = $guest->id_card_back;
                $this->existing_guest_photo    = $guest->guest_photo;
            }
        }
    }

    public function updatedCheckInDate(): void { $this->room_ids = []; }

    public function updatedCheckOutDate(): void { $this->room_ids = []; }

    private function saveBase64Image(string $base64String, string $folder = 'guest-docs'): ?string
    {
        if (!$base64String) return null;
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $data = substr($base64String, strpos($base64String, ',') + 1);
            $type = strtolower($type[1]);
            $data = base64_decode($data);
            $fileName = $folder . '_' . time() . '_' . Str::random(6) . '.' . $type;
            Storage::disk('public')->put($folder . '/' . $fileName, $data);
            return $folder . '/' . $fileName;
        }
        return null;
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
            'booking_type'    => 'required|in:Walk in,Direct website,OTA,Phone,Other',
            'id_type'         => 'nullable|in:Driving License,Aadhaar Card,Passport,Voter ID,Other',
            'guest_id_number' => 'nullable|string|max:100',
            'id_card_front'   => 'nullable|image|max:4096',
            'id_card_back'    => 'nullable|image|max:4096',
            'guest_photo'     => 'nullable|image|max:4096',
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

        // Update Guest ID & Documents if modified
        if ($this->guest_id) {
            $existingGuest = Guest::find($this->guest_id);
            if ($existingGuest) {
                $frontPath = $this->id_card_front ? $this->id_card_front->store('guest-ids', 'public') : ($this->saveBase64Image($this->id_card_front_base64, 'guest-ids') ?: $existingGuest->id_card_front);
                $backPath  = $this->id_card_back ? $this->id_card_back->store('guest-ids', 'public') : ($this->saveBase64Image($this->id_card_back_base64, 'guest-ids') ?: $existingGuest->id_card_back);
                $photoPath = $this->guest_photo ? $this->guest_photo->store('guest-photos', 'public') : ($this->saveBase64Image($this->guest_photo_base64, 'guest-photos') ?: $existingGuest->guest_photo);

                $existingGuest->update([
                    'id_type'         => $this->id_type ?: $existingGuest->id_type,
                    'id_number'       => $this->guest_id_number ?: $existingGuest->id_number,
                    'passport_number' => $this->guest_id_number ?: $existingGuest->passport_number,
                    'id_card_front'   => $frontPath,
                    'id_card_back'    => $backPath,
                    'guest_photo'     => $photoPath,
                ]);

                $this->existing_id_card_front = $frontPath;
                $this->existing_id_card_back  = $backPath;
                $this->existing_guest_photo   = $photoPath;
            }
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
            'special_notes'  => $this->special_notes,
            'status'         => $this->status,
            'booking_type'   => $this->booking_type,
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
            ]);
            $preview->setRelation('rooms', Room::whereIn('id', $this->room_ids)->get());
            $charges = $preview->calculateCharges();
            $balanceDue = round($charges['total'] - $totalPaid, 2);
        }

        return $this->view(compact('guests', 'rooms', 'payments', 'charges', 'totalPaid', 'balanceDue'));
    }
};
