<?php

use Livewire\Component;
use App\Models\Reservation;

new class extends Component
{
    public $pnr;
    public $email;
    public $reservation = null;
    public $error = null;

    public function mount()
    {
        if (request()->has('pnr')) {
            $this->pnr = trim(request()->get('pnr'));
            if (request()->has('email')) {
                $this->email = trim(request()->get('email'));
            }
            if ($this->pnr) {
                $this->trackBooking();
            }
        }
    }

    public function trackBooking()
    {
        $this->pnr = strtoupper(trim($this->pnr ?? ''));
        $this->email = strtolower(trim($this->email ?? ''));

        $this->error = null;
        $this->reservation = null;

        if (empty($this->pnr)) {
            $this->error = "Please enter a valid PNR reference code.";
            return;
        }

        if (str_starts_with($this->pnr, 'LDG-')) {
            $this->error = "'{$this->pnr}' is a Hotel Code. Track Booking requires a 6-character Room Reservation PNR (e.g. 0N9GFJ) sent in your booking confirmation email.";
            return;
        }

        // Try exact PNR + Guest Email match first (case-insensitive & trimmed)
        $reservation = Reservation::with(['hotel', 'guest', 'rooms.roomType'])
            ->whereRaw('UPPER(TRIM(pnr)) = ?', [$this->pnr])
            ->where(function ($query) {
                if (!empty($this->email)) {
                    $query->whereHas('guest', function ($q) {
                        $q->whereRaw('LOWER(TRIM(email)) = ?', [$this->email]);
                    });
                }
            })
            ->first();

        // Fallback: If email didn't match strictly, check if PNR alone exists
        if (!$reservation && !empty($this->pnr)) {
            $reservation = Reservation::with(['hotel', 'guest', 'rooms.roomType'])
                ->whereRaw('UPPER(TRIM(pnr)) = ?', [$this->pnr])
                ->first();
        }

        if ($reservation) {
            $this->reservation = $reservation;
            // Populate email if missing for display
            if (empty($this->email) && $reservation->guest) {
                $this->email = $reservation->guest->email;
            }
        } else {
            $this->reservation = null;
            $this->error = "No booking found for PNR '" . $this->pnr . "'. Please verify your 6-character PNR number (e.g., 0N9GFJ) and try again.";
        }
    }

    public function render(): mixed
    {
        return $this->view()->layout('layouts.guest');
    }
};