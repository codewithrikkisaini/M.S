<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Cash Sheet Report</title>
    <style>
        body { font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; margin: 0; padding: 0; }
        .sheet { padding: 20px 25px; page-break-after: always; }
        .sheet:last-child { page-break-after: auto; }
        
        .header-container { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 15px; }
        .hotel-name { font-size: 22px; font-weight: 900; color: #0f172a; text-transform: uppercase; margin: 0; tracking-tight: 1px; }
        .hotel-sub { font-size: 10px; color: #64748b; margin-top: 3px; font-weight: bold; }
        .report-title { font-size: 13px; font-weight: 800; color: #1e40af; background: #eff6ff; display: inline-block; padding: 4px 14px; rounded: 6px; margin-top: 8px; text-transform: uppercase; letter-spacing: 1px; }
        
        .meta-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .meta-table td { padding: 4px 0; font-size: 11px; }
        
        .summary-box { width: 100%; margin-bottom: 18px; border-collapse: collapse; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; }
        .summary-box td { padding: 8px 10px; text-align: center; border-right: 1px solid #cbd5e1; }
        .summary-box td:last-child { border-right: none; }
        .summary-val { font-size: 14px; font-weight: 900; color: #0f172a; margin-top: 2px; }
        .summary-lbl { font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .data-table th { background: #1e293b; color: #ffffff; font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 7px 8px; text-align: left; border: 1px solid #1e293b; }
        .data-table td { border: 1px solid #cbd5e1; padding: 6px 8px; font-size: 10px; vertical-align: middle; }
        .data-table tr:nth-child(even) { background: #f8fafc; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .font-black { font-weight: 900; }
        .text-emerald { color: #059669; }
        .text-rose { color: #dc2626; }
        
        .badge { font-size: 8px; font-weight: bold; padding: 2px 6px; border-radius: 4px; display: inline-block; text-transform: uppercase; }
        .badge-cash { background: #dcfce7; color: #166534; }
        .badge-upi { background: #dbeafe; color: #1e40af; }
        .badge-card { background: #f3e8ff; color: #6b21a8; }
        
        .footer-summary { width: 100%; border-collapse: collapse; margin-top: 15px; background: #0f172a; color: #ffffff; border-radius: 6px; }
        .footer-summary td { padding: 8px 12px; font-size: 11px; font-weight: bold; border-right: 1px solid #334155; }
        .footer-summary td:last-child { border-right: none; }

        .payment-breakdown { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .payment-breakdown td { padding: 4px 8px; font-size: 10px; border-bottom: 1px solid #e2e8f0; }

        .signature-table { width: 100%; margin-top: 40px; }
        .signature-line { border-top: 1px solid #94a3b8; width: 180px; text-align: center; font-size: 10px; font-weight: bold; color: #475569; padding-top: 4px; }
    </style>
</head>
<body>
    @foreach($sheets as $sheet)
    <div class="sheet">
        <!-- Hotel Header -->
        <div class="header-container">
            <h1 class="hotel-name">{{ $sheet['hotel_name'] ?? $hotelName }}</h1>
            <div class="hotel-sub">{{ $sheet['hotel_address'] ?? '' }} | Phone: {{ $sheet['hotel_phone'] ?? '' }}</div>
            <div>
                <span class="report-title">Daily Cash Sheet Report</span>
            </div>
        </div>

        <!-- Meta Info -->
        <table class="meta-table">
            <tr>
                <td><strong>Date:</strong> {{ $sheet['formatted_date'] ?? \Carbon\Carbon::parse($sheet['date'])->format('d August Y') }}</td>
                <td class="text-center"><strong>Operating Hours:</strong> {{ $sheet['time_range'] ?? '12:00 AM – 11:59 PM' }}</td>
                <td class="text-right"><strong>Generated On:</strong> {{ date('d M Y, h:i A') }}</td>
            </tr>
        </table>

        <!-- Top Summary Metric Box -->
        <table class="summary-box">
            <tr>
                <td>
                    <div class="summary-lbl">Total Customers</div>
                    <div class="summary-val">{{ $sheet['summary']['total_customers'] ?? count($sheet['rows']) }}</div>
                </td>
                <td>
                    <div class="summary-lbl">Rooms Booked</div>
                    <div class="summary-val">{{ $sheet['summary']['total_rooms'] ?? 0 }}</div>
                </td>
                <td>
                    <div class="summary-lbl">Total Revenue</div>
                    <div class="summary-val text-emerald">₹{{ number_format($sheet['summary']['total_revenue'] ?? 0, 2) }}</div>
                </td>
                <td>
                    <div class="summary-lbl">Total Paid</div>
                    <div class="summary-val text-emerald">₹{{ number_format($sheet['summary']['total_paid'] ?? 0, 2) }}</div>
                </td>
                <td>
                    <div class="summary-lbl">Total Pending / Due</div>
                    <div class="summary-val {{ ($sheet['summary']['total_due'] ?? 0) > 0 ? 'text-rose' : '' }}">
                        ₹{{ number_format($sheet['summary']['total_due'] ?? 0, 2) }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Customer Records Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 20%;">Customer Name</th>
                    <th style="width: 12%;">PNR / ID</th>
                    <th style="width: 10%;">Room</th>
                    <th style="width: 13%;">Booking Time</th>
                    <th style="width: 13%;" class="text-right">Total (₹)</th>
                    <th style="width: 13%;" class="text-right">Paid (₹)</th>
                    <th style="width: 14%;" class="text-right">Due (₹)</th>
                    <th style="width: 10%;" class="text-center">Payment</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sheet['rows'] as $idx => $row)
                <tr>
                    <td class="text-center font-bold">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ $row['name'] }}</td>
                    <td class="font-bold" style="color: #3b82f6;">{{ $row['pnr'] }}</td>
                    <td class="text-center font-bold">{{ $row['room_number'] }}</td>
                    <td class="text-center">{{ $row['booking_time'] }}</td>
                    <td class="text-right font-bold">₹{{ number_format($row['total'], 2) }}</td>
                    <td class="text-right font-bold text-emerald">₹{{ number_format($row['paid'], 2) }}</td>
                    <td class="text-right font-bold {{ $row['due'] > 0 ? 'text-rose' : '' }}">₹{{ number_format($row['due'], 2) }}</td>
                    <td class="text-center">
                        <span class="badge {{ strtolower($row['payment_method']) == 'cash' ? 'badge-cash' : (strtolower($row['payment_method']) == 'upi' ? 'badge-upi' : 'badge-card') }}">
                            {{ $row['payment_method'] }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #94a3b8;">No customer bookings recorded for this date.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer Summary Bar -->
        <table class="footer-summary">
            <tr>
                <td>Total Customers: <span class="font-black">{{ $sheet['summary']['total_customers'] ?? count($sheet['rows']) }}</span></td>
                <td>Total Rooms: <span class="font-black">{{ $sheet['summary']['total_rooms'] ?? 0 }}</span></td>
                <td class="text-right">Total Revenue: <span class="font-black">₹{{ number_format($sheet['summary']['total_revenue'] ?? 0, 2) }}</span></td>
                <td class="text-right">Total Paid: <span class="font-black" style="color: #34d399;">₹{{ number_format($sheet['summary']['total_paid'] ?? 0, 2) }}</span></td>
                <td class="text-right">Total Due: <span class="font-black" style="color: #f87171;">₹{{ number_format($sheet['summary']['total_due'] ?? 0, 2) }}</span></td>
            </tr>
        </table>

        <!-- Payment Method Breakdown -->
        <div style="margin-top: 15px;">
            <div style="font-size: 10px; font-weight: bold; color: #475569; text-transform: uppercase; margin-bottom: 5px;">Payment Method Collection Summary</div>
            <table class="payment-breakdown">
                <tr>
                    <td><strong>Cash Collected:</strong> ₹{{ number_format($sheet['totals']['cash'] ?? 0, 2) }}</td>
                    <td><strong>UPI Direct:</strong> ₹{{ number_format($sheet['totals']['upi'] ?? 0, 2) }}</td>
                    <td><strong>Credit/Debit Card:</strong> ₹{{ number_format($sheet['totals']['card'] ?? 0, 2) }}</td>
                    <td><strong>Net Banking:</strong> ₹{{ number_format($sheet['totals']['net_banking'] ?? 0, 2) }}</td>
                    <td class="text-right"><strong>Grand Collection:</strong> ₹{{ number_format($sheet['totals']['grand_total'] ?? 0, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Signatures -->
        <table class="signature-table">
            <tr>
                <td style="width: 50%;">
                    <div class="signature-line">Prepared By (Receptionist)</div>
                </td>
                <td style="width: 50%;" class="text-right">
                    <div class="signature-line" style="margin-left: auto;">Verified By (Hotel Manager)</div>
                </td>
            </tr>
        </table>
    </div>
    @endforeach
</body>
</html>
