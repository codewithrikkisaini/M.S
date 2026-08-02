<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Update</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; background-color: #f4f6f8; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        <div style="background-color: #ef4444; color: #ffffff; padding: 20px; text-align: center;">
            <h2 style="margin: 0; font-size: 20px;">Booking Update</h2>
            <p style="margin: 5px 0 0 0; font-size: 13px; opacity: 0.9;">Reservation Status Notice</p>
        </div>

        <div style="padding: 24px;">
            <p style="margin-top: 0; font-size: 15px;">Dear <strong>{{ $reservation->guest->name ?? 'Guest' }}</strong>,</p>
            
            <p style="font-size: 14px; color: #475569; line-height: 1.6;">
                We're sorry, your booking request at <strong>{{ $reservation->hotel->name ?? 'our hotel' }}</strong> could not be confirmed.
            </p>

            <div style="background-color: #fef2f2; border-radius: 8px; padding: 15px; margin: 15px 0; border: 1px solid #fecaca; font-size: 13px; color: #991b1b; line-height: 1.5;">
                Agar aapne is booking ke liye koi payment ki thi, to use aapki original payment method par refund policy ke hisaab se process kar diya jayega.
            </div>

            <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #e2e8f0;">
                <h4 style="margin: 0 0 10px 0; color: #1e293b; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px; font-size: 14px;">Booking Details</h4>
                <p style="margin: 4px 0;"><strong>Booking ID:</strong> RES-{{ $reservation->id }} (PNR: {{ $reservation->pnr }})</p>
                <p style="margin: 4px 0;"><strong>Hotel Name:</strong> {{ $reservation->hotel->name ?? 'N/A' }}</p>
                <p style="margin: 4px 0;"><strong>Check-In:</strong> {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d M Y') }}</p>
                <p style="margin: 4px 0;"><strong>Check-Out:</strong> {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d M Y') }}</p>
            </div>

            <p style="font-size: 13px; color: #64748b;">We apologize for any inconvenience caused.</p>
        </div>

        <div style="background-color: #f1f5f9; padding: 15px; text-align: center; font-size: 12px; color: #64748b;">
            Best regards,<br><strong>{{ $reservation->hotel->name ?? 'Hotel Management' }}</strong>
        </div>
    </div>
</body>
</html>
