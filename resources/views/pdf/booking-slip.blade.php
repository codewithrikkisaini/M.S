<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Slip - {{ $reservation->pnr }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 20px;
            font-size: 13px;
            line-height: 1.5;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }
        .brand-title {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .brand-sub {
            font-size: 10px;
            color: #2563eb;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .pnr-box {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 10px 15px;
            text-align: right;
            border-radius: 8px;
        }
        .pnr-label {
            font-size: 9px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
        }
        .pnr-code {
            font-size: 20px;
            font-weight: 900;
            color: #1d4ed8;
            font-family: monospace;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-confirmed {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .status-cancelled {
            background-color: #ffe4e6;
            color: #9f1239;
            border: 1px solid #fecdd3;
        }
        .section-title {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 15px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 10px;
            vertical-align: top;
            border: 1px solid #e2e8f0;
        }
        .info-label {
            font-size: 10px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            display: block;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }
        .amount-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 15px;
            border-radius: 8px;
            text-align: right;
            margin-top: 20px;
        }
        .amount-label {
            font-size: 11px;
            color: #1e40af;
            font-weight: 700;
            text-transform: uppercase;
        }
        .amount-value {
            font-size: 24px;
            font-weight: 900;
            color: #1d4ed8;
        }
        .footer-note {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            font-size: 10px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <div class="brand-title">MERAHKIE</div>
                <div class="brand-sub">Hotel Booking Confirmation Slip</div>
                <div style="font-size: 11px; color: #475569; margin-top: 5px;">
                    <strong>{{ $reservation->hotel->name ?? 'Hotel Reservation' }}</strong><br>
                    {{ $reservation->hotel->address ?? '' }}, {{ $reservation->hotel->city ?? '' }}
                </div>
            </td>
            <td style="text-align: right;">
                <div class="pnr-box">
                    <div class="pnr-label">Reference ID (PNR)</div>
                    <div class="pnr-code">{{ $reservation->pnr }}</div>
                    <div style="margin-top: 5px;">
                        @if(strtolower($reservation->status) === 'confirmed')
                            <span class="status-badge status-confirmed">Confirmed</span>
                        @elseif(strtolower($reservation->status) === 'cancelled' || strtolower($reservation->status) === 'rejected')
                            <span class="status-badge status-cancelled">Cancelled</span>
                        @else
                            <span class="status-badge status-pending">Pending</span>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Reservation Details</div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <span class="info-label">Guest Name</span>
                <span class="info-value">{{ $reservation->guest->name ?? 'Guest User' }}</span>
            </td>
            <td width="50%">
                <span class="info-label">Booking Date</span>
                <span class="info-value">{{ $reservation->created_at ? $reservation->created_at->format('d M Y, h:i A') : date('d M Y, h:i A') }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="info-label">Hotel Name</span>
                <span class="info-value">{{ $reservation->hotel->name ?? 'Hotel' }}</span>
            </td>
            <td>
                <span class="info-label">Room Details</span>
                @php
                    $room = $reservation->rooms->first();
                @endphp
                <span class="info-value">{{ $room->roomType->name ?? 'Standard Room' }} (Room #{{ $room->room_number ?? '101' }})</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="info-label">Check-In Date</span>
                <span class="info-value">{{ date('d M Y', strtotime($reservation->check_in_date)) }} (From 02:00 PM)</span>
            </td>
            <td>
                <span class="info-label">Check-Out Date</span>
                <span class="info-value">{{ date('d M Y', strtotime($reservation->check_out_date)) }} (Until 11:00 AM)</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="info-label">Guests Count</span>
                <span class="info-value">{{ $reservation->adults }} Adult/s, {{ $reservation->children }} Child/ren</span>
            </td>
            <td>
                <span class="info-label">Payment Status</span>
                @php
                    $payment = $reservation->payments->first();
                    $paymentType = $payment->payment_type ?? 'Cash';
                @endphp
                <span class="info-value">{{ $paymentType === 'Cash' ? 'Pay at Hotel (Cash)' : 'Paid Online (' . $paymentType . ')' }}</span>
            </td>
        </tr>
    </table>

    <div class="amount-box">
        <div class="amount-label">Total Amount Payable</div>
        <div class="amount-value">
            @php
                $totalPaid = $reservation->total_paid ?: ($reservation->payments->sum('amount') ?: ($room ? ($room->pivot->price ?? 2500) : 2500));
            @endphp
            ₹{{ number_format($totalPaid, 2) }}
        </div>
    </div>

    <div class="footer-note">
        <p>Thank you for choosing {{ $reservation->hotel->name ?? 'Merahkie Bookings' }}. Please present this confirmation slip along with a valid ID proof at check-in.</p>
        <p style="margin-top: 5px;">Computer generated booking voucher — No signature required.</p>
    </div>

</body>
</html>
