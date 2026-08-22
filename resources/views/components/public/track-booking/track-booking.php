<?php

use Livewire\Component;
use App\Models\Reservation;

new class extends Component
{
    public $pnr = '';
    public $email = '';
    public $reservation = null;
    public $error = null;

    public function mount()
    {
        if (request()->has('pnr') || request()->has('query') || request()->has('ref')) {
            $this->pnr = trim((string) (request()->get('pnr') ?: request()->get('query') ?: request()->get('ref')));
        }
        if (request()->has('email')) {
            $this->email = trim((string) request()->get('email'));
        }

        if ($this->pnr || $this->email) {
            $this->trackBooking();
        }
    }

    public function trackBooking()
    {
        $rawQuery = trim((string) ($this->pnr ?? ''));
        $emailInput = strtolower(trim((string) ($this->email ?? '')));

        $this->error = null;
        $this->reservation = null;

        if (empty($rawQuery) && empty($emailInput)) {
            $this->error = "Please enter your PNR Reference, Booking ID, Phone, or Email.";
            return;
        }

        $cleanTerm = trim($rawQuery);
        $cleanUpper = strtoupper($cleanTerm);
        $idOnly = preg_replace('/^#|^RES-?/i', '', $cleanTerm);

        // Extract phone variants
        $phoneDigits = preg_replace('/[^0-9]/', '', $cleanTerm);
        $phoneLast10 = strlen($phoneDigits) >= 10 ? substr($phoneDigits, -10) : $phoneDigits;
        $phoneLast8  = strlen($phoneDigits) >= 8 ? substr($phoneDigits, -8) : $phoneDigits;

        // Extract email variants
        $searchEmail = !empty($emailInput) ? $emailInput : (filter_var($cleanTerm, FILTER_VALIDATE_EMAIL) ? strtolower($cleanTerm) : null);
        if (empty($searchEmail) && str_contains($cleanTerm, '@')) {
            $searchEmail = strtolower($cleanTerm);
        }

        // Base query with tenant scope removed for public guest tracking
        $baseQuery = Reservation::withoutGlobalScopes()
            ->with([
                'hotel',
                'guest' => function ($g) { $g->withoutGlobalScopes(); },
                'rooms' => function ($r) { $r->withoutGlobalScopes()->with('roomType'); },
                'payments' => function ($p) { $p->withoutGlobalScopes(); }
            ]);

        $reservation = null;

        // 1. Match exact PNR code (case-insensitive)
        if (!empty($cleanUpper)) {
            $reservation = (clone $baseQuery)
                ->where(function ($q) use ($cleanUpper, $cleanTerm) {
                    $q->whereRaw('UPPER(TRIM(pnr)) = ?', [$cleanUpper])
                      ->orWhere('pnr', $cleanTerm);
                })
                ->latest()
                ->first();
        }

        // 2. Match Booking Reference ID (e.g., RES-15, #15, or 15)
        if (!$reservation && !empty($idOnly) && preg_match('/^\d{1,8}$/', $idOnly)) {
            $reservation = (clone $baseQuery)
                ->where('id', (int) $idOnly)
                ->latest()
                ->first();
        }

        // 3. Match Guest Phone Number (handles country codes +91, 0, spaces, dashes)
        if (!$reservation && !empty($phoneDigits) && strlen($phoneDigits) >= 4) {
            $reservation = (clone $baseQuery)
                ->whereHas('guest', function ($g) use ($cleanTerm, $phoneDigits, $phoneLast10, $phoneLast8) {
                    $g->withoutGlobalScopes()
                      ->where(function ($q) use ($cleanTerm, $phoneDigits, $phoneLast10, $phoneLast8) {
                          $q->where('phone', 'LIKE', "%{$cleanTerm}%")
                            ->orWhere('phone', 'LIKE', "%{$phoneDigits}%")
                            ->orWhere('phone', 'LIKE', "%{$phoneLast10}%")
                            ->orWhere('phone', 'LIKE', "%{$phoneLast8}%")
                            ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+', ''), '(', ''), ')', '') LIKE ?", ["%{$phoneLast10}%"])
                            ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+', ''), '(', ''), ')', '') LIKE ?", ["%{$phoneDigits}%"])
                            ->orWhereRaw("? LIKE CONCAT('%', REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+', ''), '(', ''), ')', ''), '%')", [$phoneDigits]);
                      });
                })
                ->latest()
                ->first();
        }

        // 4. Match Guest Email
        if (!$reservation && !empty($searchEmail)) {
            $reservation = (clone $baseQuery)
                ->whereHas('guest', function ($g) use ($searchEmail) {
                    $g->withoutGlobalScopes()
                      ->whereRaw('LOWER(TRIM(email)) = ?', [$searchEmail])
                      ->orWhere('email', 'LIKE', "%{$searchEmail}%");
                })
                ->latest()
                ->first();
        }

        // 5. Match Partial PNR, Hotel Name, or Guest Name
        if (!$reservation && !empty($cleanTerm) && strlen($cleanTerm) >= 2) {
            $reservation = (clone $baseQuery)
                ->where(function ($q) use ($cleanTerm, $cleanUpper) {
                    $q->where('pnr', 'LIKE', "%{$cleanUpper}%")
                      ->orWhereHas('hotel', function ($h) use ($cleanTerm) {
                          $h->where('name', 'LIKE', "%{$cleanTerm}%");
                      })
                      ->orWhereHas('guest', function ($g) use ($cleanTerm) {
                          $g->withoutGlobalScopes()
                            ->where('name', 'LIKE', "%{$cleanTerm}%");
                      });
                })
                ->latest()
                ->first();
        }

        if ($reservation) {
            $this->reservation = $reservation;
            $this->pnr = $reservation->pnr;
            if (empty($this->email) && $reservation->guest) {
                $this->email = $reservation->guest->email;
            }
        } else {
            $this->reservation = null;
            $searchTerm = !empty($rawQuery) ? "'{$rawQuery}'" : "'{$emailInput}'";
            $this->error = "No booking found matching {$searchTerm}. Please check your PNR, Booking ID, Phone, or Email and try again.";
        }
    }

    public function render(): mixed
    {
        return $this->view()->layout('layouts.guest');
    }
};