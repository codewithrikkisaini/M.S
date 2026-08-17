<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Payment;
use App\Services\ReservationService;
use App\Services\GuestBlacklistService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

new class extends Component
{
    use WithFileUploads;
    public string $guest_id = '', $check_in_date = '', $check_out_date = '';
    public array $room_ids = [];
    public int $adults = 1, $children = 0;
    public string $special_notes = '';
    public string $discount_type = 'Fixed', $discount_value = '0';
    public string $tax_rate = '18';

    public string $payment_type = 'Cash', $payment_amount = '';
    public bool $is_new_guest = false;

    public string $new_guest_name = '';
    public string $new_guest_email = '';
    public string $new_guest_phone = '';

    public string $id_type = '';
    public string $guest_id_number = '';

    // ID Cards & Guest Photo File Uploads & Base64 Camera Captures
    public $id_card_front;
    public $id_card_back;
    public $guest_photo;
    public string $id_card_front_base64 = '';
    public string $id_card_back_base64 = '';
    public string $guest_photo_base64 = '';

    public string $booking_type = 'Walk in';
    public string $captcha_question = '';
    public string $captcha_answer = '';
    public string $captcha_input = '';

    // Blacklist check
    public bool $is_blacklisted = false;
    public string $blacklist_reason = '';

    public function mount(): void
    {
        $this->check_in_date = request()->query('check_in_date', '');
        $this->check_out_date = request()->query('check_out_date', '');
        $roomId = request()->query('room_id', '');
        if ($roomId) {
            $this->room_ids = [(int)$roomId];
        }

        $this->regenerateCaptcha();
    }

    public function regenerateCaptcha(): void
    {
        $first = random_int(3, 9);
        $second = random_int(2, 8);
        $this->captcha_question = $first . ' + ' . $second;
        $this->captcha_answer = (string) ($first + $second);
        $this->captcha_input = '';
    }

    public function updatedCheckInDate(): void { $this->room_ids = []; }

    public function updatedCheckOutDate(): void { $this->room_ids = []; }

    public function updatedGuestId($value): void
    {
        $this->is_blacklisted = false;
        $this->blacklist_reason = '';

        if ($value) {
            $guest = Guest::find($value);
            if ($guest) {
                $this->id_type = $guest->id_type ?? '';
                $this->guest_id_number = $guest->id_number ?? $guest->passport_number ?? '';

                // Check blacklist
                $blacklistService = app(GuestBlacklistService::class);
                $match = $blacklistService->isGuestBlacklisted($guest);
                if ($match) {
                    $this->is_blacklisted = true;
                    $this->blacklist_reason = $match->reason;
                }
            }
        }
    }

    public function updatedRoomIds(): void
    {
        if (!empty($this->room_ids)) {
            $selectedRoom = Room::with('roomType')->whereIn('id', $this->room_ids)->first();
            if ($selectedRoom && $selectedRoom->roomType && $selectedRoom->roomType->tax_percent !== null) {
                $this->tax_rate = (string) $selectedRoom->roomType->tax_percent;
            }
        }
    }

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
        $blacklistService = app(GuestBlacklistService::class);

        // HARD BACKEND BLACKLIST CHECK — does NOT depend on frontend $is_blacklisted flag
        // This runs fresh on every submit for EVERY role (Admin, Reception, Super Admin)
        if (!$this->is_new_guest && !empty($this->guest_id)) {
            $guest = Guest::find($this->guest_id);
            if ($guest) {
                $match = $blacklistService->isGuestBlacklisted($guest);
                if ($match) {
                    $this->is_blacklisted = true;
                    $this->blacklist_reason = $match->reason;
                    $this->addError('guest_id', 'This guest is currently blacklisted and cannot make new reservations. Reason: ' . $match->reason);
                    return;
                }
            }
        }

        // Prevent saving if frontend already flagged guest as blacklisted
        if ($this->is_blacklisted) {
            $this->addError('guest_id', 'This guest is currently blacklisted and cannot make new reservations. Reason: ' . $this->blacklist_reason);
            return;
        }

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
            'payment_type'    => 'required|in:Cash,Card,UPI',
            'payment_amount'  => 'nullable|numeric|min:0',
            'booking_type'    => 'required|in:Walk in,Direct website,OTA,Phone,Other',
            'captcha_input'   => 'required|numeric',
        ];

        if ($this->is_new_guest) {
            $rules['new_guest_name']  = 'required|string|max:255';
            $rules['new_guest_email'] = 'nullable|email|unique:guests,email';
            $rules['new_guest_phone'] = 'nullable|string|max:20';
            $rules['id_type']         = 'nullable|in:Driving License,Aadhaar Card,Passport,Voter ID,Other';
            $rules['guest_id_number'] = 'nullable|string|max:100';
            $rules['id_card_front']   = 'nullable|image|max:4096';
            $rules['id_card_back']    = 'nullable|image|max:4096';
            $rules['guest_photo']     = 'nullable|image|max:4096';
        } else {
            $rules['guest_id'] = 'required|exists:guests,id';
        }

        $this->validate($rules);

        if ((string) $this->captcha_input !== (string) $this->captcha_answer) {
            $this->regenerateCaptcha();
            $this->addError('captcha_input', 'CAPTCHA answer is incorrect. Please try again.');
            return;
        }

        if ($this->is_new_guest) {
            // HARD BACKEND BLACKLIST CHECK for new guest identity
            $nameParts = explode(' ', trim($this->new_guest_name), 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';
            $match = $blacklistService->isIdentityBlacklisted(
                $firstName,
                $lastName,
                $this->guest_id_number ?: null,
                null
            );
            if ($match) {
                $this->addError('new_guest_name', 'This guest identity is currently blacklisted and cannot make new reservations. Reason: ' . $match->reason);
                return;
            }

            $guest_id_str = 'G-' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT);
            while (Guest::where('guest_id', $guest_id_str)->exists()) {
                $guest_id_str = 'G-' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT);
            }

            $frontPath = $this->id_card_front ? $this->id_card_front->store('guest-ids', 'public') : $this->saveBase64Image($this->id_card_front_base64, 'guest-ids');
            $backPath  = $this->id_card_back ? $this->id_card_back->store('guest-ids', 'public') : $this->saveBase64Image($this->id_card_back_base64, 'guest-ids');
            $photoPath = $this->guest_photo ? $this->guest_photo->store('guest-photos', 'public') : $this->saveBase64Image($this->guest_photo_base64, 'guest-photos');

            $guest = Guest::create([
                'guest_id'      => $guest_id_str,
                'name'          => $this->new_guest_name,
                'email'         => $this->new_guest_email ?: null,
                'phone'         => $this->new_guest_phone ?: null,
                'id_type'       => $this->id_type ?: null,
                'id_number'     => $this->guest_id_number ?: null,
                'passport_number' => $this->guest_id_number ?: null,
                'id_card_front' => $frontPath,
                'id_card_back'  => $backPath,
                'guest_photo'   => $photoPath,
            ]);
            $this->guest_id = (string)$guest->id;
        } else {
            if ($this->guest_id && ($this->id_type || $this->guest_id_number)) {
                $existingGuest = Guest::find($this->guest_id);
                if ($existingGuest) {
                    $existingGuest->update([
                        'id_type'   => $this->id_type ?: $existingGuest->id_type,
                        'id_number' => $this->guest_id_number ?: $existingGuest->id_number,
                    ]);
                }
            }
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


        try {
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
                'special_notes'  => $this->special_notes,
                'booking_type' => $this->booking_type,
                'status'         => 'Confirmed',
            ], false);
        } catch (\App\Exceptions\GuestBlacklistedException $e) {
            $this->addError('guest_id', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->addError('guest_id', $e->getMessage());
            return;
        }

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
            ]);
            $preview->setRelation('rooms', Room::whereIn('id', $this->room_ids)->get());
            $charges = $preview->calculateCharges();
            $balanceDue = round($charges['total'] - (float) ($this->payment_amount !== '' ? $this->payment_amount : 0), 2);
        }

        return $this->view(compact('guests', 'rooms', 'charges', 'balanceDue'));
    }
};
