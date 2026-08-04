<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingSlipController extends Controller
{
    public function download($pnr)
    {
        $reservation = Reservation::with(['guest', 'hotel', 'rooms.roomType', 'payments'])
            ->where('pnr', $pnr)
            ->first();

        if (!$reservation) {
            $reservation = Reservation::with(['guest', 'hotel', 'rooms.roomType', 'payments'])
                ->find($pnr);
        }

        if (!$reservation) {
            abort(404, 'Booking reservation slip not found.');
        }

        $pdf = Pdf::loadView('pdf.booking-slip', compact('reservation'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Booking-Slip-' . $reservation->pnr . '.pdf');
    }
}
