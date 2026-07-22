<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Reservation;
use App\Models\Payment;
use Carbon\Carbon;

class DailyCashSheetService
{
    public function build(string $date): array
    {
        $day = Carbon::parse($date)->startOfDay();

        $reservations = Reservation::with(['guest', 'rooms.roomType', 'payments'])
            ->where('status', '!=', 'Cancelled')
            ->whereDate('check_in_date', '<=', $day)
            ->whereDate('check_out_date', '>=', $day)
            ->get();

        // Map room_id => reservation occupying it on this date
        $occupancyByRoom = [];
        foreach ($reservations as $reservation) {
            foreach ($reservation->rooms as $room) {
                $occupancyByRoom[$room->id] = [
                    'reservation' => $reservation,
                    'rate'        => (float) ($room->roomType ? $room->roomType->daily_rate : ($room->pivot->price ?? $room->price ?? 0)),
                ];
            }
        }

        $rows = Room::orderBy('room_number')->get()->map(function ($room) use ($occupancyByRoom) {
            $occupied = $occupancyByRoom[$room->id] ?? null;

            if (!$occupied) {
                return [
                    'room_number'    => $room->room_number,
                    'name'           => null,
                    'rent'           => null,
                    'tax'            => null,
                    'misc'           => null,
                    'arrival_date'   => null,
                    'departure_date' => null,
                    'balance_due'    => null,
                    'paid'           => null,
                ];
            }

            $reservation = $occupied['reservation'];
            $rate = $occupied['rate'];

            return [
                'room_number'    => $room->room_number,
                'name'           => $reservation->guest->name ?? 'N/A',
                'rent'           => $rate,
                'tax'            => round($rate * ((float) $reservation->tax_rate / 100), 2),
                'misc'           => round((float) $reservation->misc_charge, 2),
                'arrival_date'   => $reservation->check_in_date,
                'departure_date' => $reservation->check_out_date,
                'balance_due'    => $reservation->balance_due,
                'paid'           => $reservation->total_paid,
            ];
        });

        $payments = Payment::whereDate('paid_at', $day)->get();

        $totals = [
            'cash' => (float) $payments->where('payment_type', 'Cash')->sum('amount'),
            'card' => (float) $payments->where('payment_type', 'Card')->sum('amount'),
            'upi'  => (float) $payments->where('payment_type', 'UPI')->sum('amount'),
        ];
        $totals['grand_total'] = round($totals['cash'] + $totals['card'] + $totals['upi'], 2);

        return [
            'date'   => $day->toDateString(),
            'rows'   => $rows,
            'totals' => $totals,
        ];
    }

    public function buildRange(string $from, string $to): array
    {
        $start = Carbon::parse($from)->startOfDay();
        $end = Carbon::parse($to)->startOfDay();

        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        $sheets = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $sheets[] = $this->build($cursor->toDateString());
            $cursor->addDay();
        }

        return $sheets;
    }
}
