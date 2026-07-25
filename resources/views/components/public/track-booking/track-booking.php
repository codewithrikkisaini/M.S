<?php

use Livewire\Component;
use App\Models\Reservation;

new class extends Component
{
    public $pnr;
    public $email;
    public $reservation = null;
    public $error = null;

    public function trackBooking()
    {
        $this->validate([
            'pnr' => 'required|string',
            'email' => 'required|email'
        ]);

        $this->error = null;

        $reservation = Reservation::with(['hotel', 'guest', 'rooms.roomType'])
            ->where('pnr', strtoupper($this->pnr))
            ->whereHas('guest', function ($q) {
                $q->where('email', $this->email);
            })->first();

        if ($reservation) {
            $this->reservation = $reservation;
        } else {
            $this->reservation = null;
            $this->error = "No booking found with this PNR and Email combination.";
        }
    }

    public function render(): mixed
    {
        return $this->view()->layout('layouts.guest');
    }
};