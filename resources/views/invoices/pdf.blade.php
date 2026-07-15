<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .table { width: 100%; text-align: left; border-collapse: collapse; }
        .table td, .table th { padding: 8px; border-bottom: 1px solid #ddd; }
        .text-right { text-align: right; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <h2>{{ $hotelName }} - Invoice</h2>
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td>
                    <strong>Invoice Number:</strong> {{ $invoice->invoice_number }}<br>
                    <strong>Booking Code:</strong> {{ $invoice->checkout->reservation->checkIn->booking_code ?? 'N/A' }}<br>
                    <strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}
                </td>
                <td style="text-align: right;">
                    <strong>Guest:</strong> {{ $invoice->checkout->reservation->guest->name }}<br>
                    <strong>Email:</strong> {{ $invoice->checkout->reservation->guest->email }}<br>
                    <strong>Phone:</strong> {{ $invoice->checkout->reservation->guest->phone }}
                </td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->checkout->reservation->rooms as $room)
                <tr>
                    <td>Room {{ $room->room_number }} ({{ $room->roomType->name ?? '' }})</td>
                    <td>${{ number_format($room->pivot->price ?? $room->price, 2) }} / night</td>
                </tr>
                @endforeach
                <tr>
                    <td>Check-In</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->checkout->reservation->check_in_date)->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td>Check-Out</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->checkout->reservation->check_out_date)->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td>Total Nights</td>
                    <td>{{ $invoice->checkout->nights }}</td>
                </tr>
            </tbody>
        </table>

        <table style="width: 100%; margin-top: 20px; border-top: 2px solid #000; padding-top: 10px;">
            <tr>
                <td style="text-align: right; width: 80%;"><strong>Subtotal:</strong></td>
                <td style="text-align: right;">${{ number_format($invoice->checkout->subtotal, 2) }}</td>
            </tr>
            @if($invoice->checkout->discount > 0)
            <tr>
                <td style="text-align: right;"><strong>Discount:</strong></td>
                <td style="text-align: right;">-${{ number_format($invoice->checkout->discount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td style="text-align: right;"><strong>Tax ({{ rtrim(rtrim(number_format($invoice->checkout->tax_rate, 2), '0'), '.') }}%):</strong></td>
                <td style="text-align: right;">${{ number_format($invoice->checkout->tax, 2) }}</td>
            </tr>
            <tr>
                <td style="text-align: right;"><strong>Grand Total:</strong></td>
                <td style="text-align: right;"><strong>${{ number_format($invoice->checkout->total_amount, 2) }}</strong></td>
            </tr>
        </table>

        <table class="table" style="margin-top: 20px;">
            <thead>
                <tr><th>Payment Date</th><th>Method</th><th class="text-right">Amount</th></tr>
            </thead>
            <tbody>
                @foreach($invoice->checkout->reservation->payments as $payment)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y h:i A') }}</td>
                    <td>{{ $payment->payment_type }}</td>
                    <td class="text-right">${{ number_format($payment->amount, 2) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="text-align: right;"><strong>Total Paid:</strong></td>
                    <td class="text-right"><strong>${{ number_format($invoice->checkout->reservation->payments->sum('amount'), 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <p style="margin-top: 50px; text-align: center; color: #777;">{{ $invoiceFooter ?? 'Thank you for staying with us!' }}</p>
    </div>
</body>
</html>
