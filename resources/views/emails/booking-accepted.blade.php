<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Room Booking Confirmed</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; background-color: #f4f6f8; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        <div style="background-color: #10b981; color: #ffffff; padding: 20px; text-align: center;">
            <h2 style="margin: 0; font-size: 22px;">Room Booking Confirmed 🎉</h2>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Congratulations!</p>
        </div>

        <div style="padding: 24px;">
            <p style="margin-top: 0; font-size: 16px; color: #1e293b;">Dear <strong>{{ $reservation->guest->name ?? 'Guest' }}</strong>,</p>
            
            <p style="font-size: 15px; color: #047857; font-weight: 600; line-height: 1.6;">
                Congratulations! Your room booking successfully Confirmed.
            </p>
            <p style="font-size: 14px; color: #334155; line-height: 1.6;">
                <strong>{{ $reservation->hotel->name ?? 'Hotel' }}</strong> . We look forward to welcoming you!
            </p>

            <div style="background-color: #f8fafc; padding: 18px; border-radius: 10px; margin: 20px 0; border: 1px solid #e2e8f0;">
                <h4 style="margin: 0 0 12px 0; color: #1e293b; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px; font-size: 15px;">Booking Details</h4>
                <p style="margin: 5px 0;"><strong>Booking ID:</strong> <span style="color: #10b981; font-weight: bold;">RES-{{ $reservation->id }} (PNR: {{ $reservation->pnr }})</span></p>
                <p style="margin: 5px 0;"><strong>Hotel Name:</strong> <strong>{{ $reservation->hotel->name ?? 'N/A' }}</strong></p>
                <p style="margin: 5px 0;"><strong>Room Name:</strong> 
                    @if($reservation->rooms && $reservation->rooms->count() > 0)
                        {{ $reservation->rooms->map(fn($r) => 'Room ' . $r->room_number . ' (' . ($r->roomType->name ?? 'Standard') . ')')->join(', ') }}
                    @else
                        Standard Room
                    @endif
                </p>
                <p style="margin: 5px 0;"><strong>Check-In:</strong> {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d M Y') }}</p>
                <p style="margin: 5px 0;"><strong>Check-Out:</strong> {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d M Y') }}</p>
                <p style="margin: 5px 0;"><strong>Total Amount:</strong> <strong style="color: #059669;">${{ number_format($reservation->payments->first()->amount ?? $reservation->total_amount ?? 0, 2) }}</strong></p>
            </div>

            <p style="font-size: 13px; color: #64748b;">If you have any questions or require special arrangements, please reach out to the hotel.</p>
        </div>

        <div style="background-color: #f1f5f9; padding: 15px; text-align: center; font-size: 12px; color: #64748b;">
            Thank you for choosing Lodgiko platform!<br><strong>{{ $reservation->hotel->name ?? 'Hotel Management' }}</strong>
        </div>
    </div>
</body>
</html>
