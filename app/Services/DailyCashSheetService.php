<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Hotel;
use Carbon\Carbon;

class DailyCashSheetService
{
    public function build(string $date): array
    {
        $day = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        // Get hotel settings
        $userHotel = auth()->user()?->hotel;
        $hotelName = $userHotel?->name ?? Setting::get('hotel_name', 'Lodgiko Hotel & Resort');
        $hotelAddress = $userHotel?->address ?? Setting::get('hotel_address', 'Main Street, Luxury Zone');
        $hotelPhone = $userHotel?->phone ?? Setting::get('hotel_phone', '+91 9876543210');
        $hotelEmail = $userHotel?->email ?? Setting::get('hotel_email', 'info@hotel.com');

        // Get reservations active or created on this date
        $reservations = Reservation::with(['guest', 'rooms.roomType', 'payments'])
            ->where('status', '!=', 'Cancelled')
            ->where(function($q) use ($day, $endOfDay) {
                $q->whereBetween('created_at', [$day, $endOfDay])
                  ->orWhere(function($sub) use ($day) {
                      $sub->whereDate('check_in_date', '<=', $day)
                          ->whereDate('check_out_date', '>=', $day);
                  });
            })
            ->latest()
            ->get();

        $rows = [];
        $totalRevenue = 0;
        $totalPaid = 0;
        $totalDue = 0;

        foreach ($reservations as $res) {
            $charges = $res->calculateCharges();
            $rent = $charges['subtotal'];
            $tax = $charges['tax'];
            $total = $charges['total'];
            $paid = $res->total_paid;
            $due = max(0, $res->balance_due);

            $totalRevenue += $total;
            $totalPaid += $paid;
            $totalDue += $due;

            $paymentMethod = $res->payments->last()?->payment_type ?: 'Cash';
            if ($paid == 0) {
                $paymentMethod = 'Pay at Hotel';
            }

            $rows[] = [
                'reservation_id' => $res->id,
                'pnr'            => $res->pnr ?: 'PNR-' . str_pad($res->id, 5, '0', STR_PAD_LEFT),
                'name'           => $res->guest->name ?? 'Guest',
                'phone'          => $res->guest->phone ?? '—',
                'room_number'    => $res->rooms->pluck('room_number')->implode(', ') ?: '—',
                'room_type'      => $res->rooms->map(fn($r) => $r->roomType?->name ?: 'Standard')->unique()->implode(', ') ?: 'Standard Room',
                'booking_time'   => $res->created_at ? $res->created_at->format('h:i A') : '12:00 PM',
                'arrival_date'   => $res->check_in_date,
                'departure_date' => $res->check_out_date,
                'rent'           => $rent,
                'tax'            => $tax,
                'total'          => $total,
                'paid'           => $paid,
                'due'            => $due,
                'payment_method' => $paymentMethod,
                'status'         => $res->status ?: 'Confirmed',
            ];
        }

        // Daily payment breakdown
        $payments = Payment::whereDate('paid_at', $day)->get();
        $totals = [
            'cash' => (float) $payments->where('payment_type', 'Cash')->sum('amount'),
            'card' => (float) $payments->where('payment_type', 'Card')->sum('amount'),
            'upi'  => (float) $payments->where('payment_type', 'UPI')->sum('amount'),
            'net_banking' => (float) $payments->where('payment_type', 'Net Banking')->sum('amount'),
        ];
        $totals['grand_total'] = round($totals['cash'] + $totals['card'] + $totals['upi'] + $totals['net_banking'], 2);

        $summary = [
            'total_customers' => count($rows),
            'total_rooms'     => $reservations->flatMap(fn($r) => $r->rooms)->unique('id')->count(),
            'total_revenue'   => $totalRevenue,
            'total_paid'      => $totalPaid,
            'total_due'       => $totalDue,
        ];

        return [
            'hotel_name'     => $hotelName,
            'hotel_address'  => $hotelAddress,
            'hotel_phone'    => $hotelPhone,
            'hotel_email'    => $hotelEmail,
            'date'           => $day->toDateString(),
            'formatted_date' => $day->format('d August Y'),
            'time_range'     => '12:00 AM – 11:59 PM',
            'rows'           => $rows,
            'totals'         => $totals,
            'summary'        => $summary,
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

    public function getCustomerData($reservationId): ?array
    {
        $res = Reservation::with(['guest', 'rooms.roomType', 'payments'])
            ->find($reservationId);

        if (!$res) return null;

        $charges = $res->calculateCharges();
        $resHotel = $res->hotel ?? auth()->user()?->hotel;
        $hotelName = $resHotel?->name ?? Setting::get('hotel_name', 'Lodgiko Hotel & Resort');
        $hotelAddress = $resHotel?->address ?? Setting::get('hotel_address', 'Main Street, Luxury Zone');
        $hotelPhone = $resHotel?->phone ?? Setting::get('hotel_phone', '+91 9876543210');
        $hotelEmail = $resHotel?->email ?? Setting::get('hotel_email', 'info@hotel.com');

        $paymentMethod = $res->payments->last()?->payment_type ?: 'Cash';
        if ($res->total_paid == 0) {
            $paymentMethod = 'Pay at Hotel';
        }

        return [
            'hotel_name'     => $hotelName,
            'hotel_address'  => $hotelAddress,
            'hotel_phone'    => $hotelPhone,
            'hotel_email'    => $hotelEmail,
            'guest_name'     => $res->guest->name ?? 'Guest',
            'guest_email'    => $res->guest->email ?? '—',
            'guest_phone'    => $res->guest->phone ?? '—',
            'pnr'            => $res->pnr ?: 'PNR-' . str_pad($res->id, 5, '0', STR_PAD_LEFT),
            'booking_id'     => 'RES-' . $res->id . '-' . date('Y', strtotime($res->created_at)),
            'room_number'    => $res->rooms->pluck('room_number')->implode(', ') ?: '101',
            'room_type'      => $res->rooms->map(fn($r) => $r->roomType?->name ?: 'Standard')->unique()->implode(', ') ?: 'Standard Room',
            'guests_count'   => ($res->adults ?? 1) . ' Adult(s)' . ($res->children > 0 ? ', ' . $res->children . ' Child' : ''),
            'checkin_datetime'  => Carbon::parse($res->check_in_date)->format('d M Y') . ' (12:00 PM)',
            'checkout_datetime' => Carbon::parse($res->check_out_date)->format('d M Y') . ' (11:00 AM)',
            'booking_datetime'  => $res->created_at ? $res->created_at->format('d M Y, h:i A') : '—',
            'subtotal'       => $charges['subtotal'],
            'tax_rate'       => $charges['tax_rate'],
            'tax'            => $charges['tax'],
            'discount'       => $charges['discount'],
            'total_amount'   => $charges['total'],
            'paid_amount'    => $res->total_paid,
            'due_amount'     => max(0, $res->balance_due),
            'payment_method' => $paymentMethod,
            'status'         => $res->status ?: 'Confirmed',
        ];
    }
}
