<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Receipt - {{ $customer['pnr'] }}</title>
    <style>
        body { font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; margin: 0; padding: 20px 25px; }
        
        .header { width: 100%; border-bottom: 2px solid #0f172a; padding-bottom: 15px; margin-bottom: 20px; }
        .hotel-name { font-size: 22px; font-weight: 900; color: #0f172a; text-transform: uppercase; margin: 0; }
        .hotel-sub { font-size: 10px; color: #64748b; margin-top: 3px; font-weight: bold; }
        .receipt-title { font-size: 14px; font-weight: 900; color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe; padding: 4px 12px; border-radius: 6px; text-transform: uppercase; letter-spacing: 1px; float: right; }
        
        .clear { clear: both; }
        
        .info-grid { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-box { border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; background: #f8fafc; vertical-align: top; }
        .info-title { font-size: 10px; font-weight: 900; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px; margin-bottom: 8px; }
        
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 4px 0; font-size: 11px; }
        .info-label { font-weight: bold; color: #64748b; width: 40%; }
        .info-value { font-weight: 800; color: #0f172a; }
        
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .details-table th { background: #0f172a; color: #ffffff; font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 8px 10px; text-align: left; }
        .details-table td { border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 11px; }
        
        .summary-wrapper { width: 100%; margin-bottom: 25px; }
        .summary-table { width: 50%; float: right; border-collapse: collapse; }
        .summary-table td { padding: 6px 10px; font-size: 11px; border-bottom: 1px solid #e2e8f0; }
        .summary-table tr:last-child td { border-bottom: 2px solid #0f172a; }
        
        .stamp { font-size: 18px; font-weight: 900; text-transform: uppercase; padding: 8px 16px; border-radius: 8px; display: inline-block; letter-spacing: 2px; }
        .stamp-paid { color: #166534; border: 3px solid #22c55e; background: #f0fdf4; }
        .stamp-due { color: #991b1b; border: 3px solid #ef4444; background: #fef2f2; }
        
        .footer { margin-top: 40px; border-top: 1px solid #cbd5e1; padding-top: 15px; text-align: center; color: #64748b; font-size: 10px; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-black { font-weight: 900; }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <span class="receipt-title">Customer Receipt</span>
        <h1 class="hotel-name">{{ $customer['hotel_name'] }}</h1>
        <div class="hotel-sub">{{ $customer['hotel_address'] }} | Phone: {{ $customer['hotel_phone'] }} | Email: {{ $customer['hotel_email'] }}</div>
        <div class="clear"></div>
    </div>

    <!-- Info Section: Guest & Booking Details -->
    <table class="info-grid">
        <tr>
            <td style="width: 48%;" class="info-box">
                <div class="info-title">Guest Details</div>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Customer Name:</td>
                        <td class="info-value">{{ $customer['guest_name'] }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Contact Phone:</td>
                        <td class="info-value">{{ $customer['guest_phone'] }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Email Address:</td>
                        <td class="info-value">{{ $customer['guest_email'] }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">ID Type:</td>
                        <td class="info-value" style="color: #4f46e5;">{{ $customer['id_type'] ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">ID Number:</td>
                        <td class="info-value">{{ $customer['id_number'] ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Guests Count:</td>
                        <td class="info-value">{{ $customer['guests_count'] }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%;" class="info-box">
                <div class="info-title">Booking Information</div>
                <table class="info-table">
                    <tr>
                        <td class="info-label">PNR / Reference:</td>
                        <td class="info-value" style="color: #2563eb;">{{ $customer['pnr'] }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Booking ID:</td>
                        <td class="info-value">{{ $customer['booking_id'] }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Booking Date/Time:</td>
                        <td class="info-value">{{ $customer['booking_datetime'] }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Booking Status:</td>
                        <td class="info-value" style="text-transform: uppercase; color: #166534;">{{ $customer['status'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Room & Stay Details Table -->
    <table class="details-table">
        <thead>
            <tr>
                <th>Room No.</th>
                <th>Room Type</th>
                <th>Check-in Date & Time</th>
                <th>Check-out Date & Time</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="font-black text-center" style="font-size: 13px;">{{ $customer['room_number'] }}</td>
                <td class="font-black">{{ $customer['room_type'] }}</td>
                <td>{{ $customer['checkin_datetime'] }}</td>
                <td>{{ $customer['checkout_datetime'] }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Payment & Charges Breakdown -->
    <div class="summary-wrapper">
        <!-- Status Stamp -->
        <div style="float: left; width: 45%; padding-top: 15px;">
            @if($customer['due_amount'] <= 0)
                <div class="stamp stamp-paid">✓ FULLY PAID</div>
            @endif
        </div>

        <!-- Charges Summary Table -->
        <table class="summary-table">
            <tr>
                <td class="info-label">Room Charges (Rent):</td>
                <td class="text-right font-black">₹{{ number_format($customer['subtotal'], 2) }}</td>
            </tr>
            @if($customer['discount'] > 0)
            <tr>
                <td class="info-label">Discount:</td>
                <td class="text-right font-black" style="color: #dc2626;">- ₹{{ number_format($customer['discount'], 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="info-label">Taxes ({{ $customer['tax_rate'] }}%):</td>
                <td class="text-right font-black">₹{{ number_format($customer['tax'], 2) }}</td>
            </tr>
            <tr style="background: #f1f5f9;">
                <td class="info-label" style="font-size: 12px; color: #0f172a;"><strong>Total Amount:</strong></td>
                <td class="text-right font-black" style="font-size: 13px;">₹{{ number_format($customer['total_amount'], 2) }}</td>
            </tr>
            <tr>
                <td class="info-label" style="color: #166534;">Amount Paid:</td>
                <td class="text-right font-black" style="color: #166534; font-size: 12px;">₹{{ number_format($customer['paid_amount'], 2) }}</td>
            </tr>
            <tr style="background: #fdf2f2;">
                <td class="info-label" style="color: #991b1b;"><strong>Pending / Due:</strong></td>
                <td class="text-right font-black" style="color: #dc2626; font-size: 13px;">₹{{ number_format($customer['due_amount'], 2) }}</td>
            </tr>
        </table>
        <div class="clear"></div>
    </div>

    <!-- Footer Note -->
    <div class="footer">
        Thank you for choosing {{ $customer['hotel_name'] }}! We wish you a pleasant stay.<br>
        This is a computer-generated official cash receipt and does not require a physical signature.
    </div>

</body>
</html>
