<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingSlipController extends Controller
{
    public function download($pnr)
    {
        $cleanPnr = trim(strtoupper((string) $pnr));
        $reservation = Reservation::withoutGlobalScopes()
            ->with([
                'guest' => fn($q) => $q->withoutGlobalScopes(),
                'hotel',
                'rooms' => fn($q) => $q->withoutGlobalScopes()->with('roomType'),
                'payments' => fn($q) => $q->withoutGlobalScopes()
            ])
            ->where(function ($q) use ($cleanPnr, $pnr) {
                $q->whereRaw('UPPER(TRIM(pnr)) = ?', [$cleanPnr])
                  ->orWhere('pnr', $pnr);
            })
            ->first();

        if (!$reservation && is_numeric($pnr)) {
            $reservation = Reservation::withoutGlobalScopes()
                ->with([
                    'guest' => fn($q) => $q->withoutGlobalScopes(),
                    'hotel',
                    'rooms' => fn($q) => $q->withoutGlobalScopes()->with('roomType'),
                    'payments' => fn($q) => $q->withoutGlobalScopes()
                ])
                ->find((int) $pnr);
        }

        if (!$reservation) {
            abort(404, 'Booking reservation slip not found.');
        }

        $pdf = Pdf::loadView('pdf.booking-slip', compact('reservation'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Booking-Slip-' . $reservation->pnr . '.pdf');
    }
}
