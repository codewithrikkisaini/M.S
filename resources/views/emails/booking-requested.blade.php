<!DOCTYPE html>
<html>
<head>
    <title>Booking Requested</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-w-2xl mx-auto p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        <h2 style="color: #2563eb;">Booking Requested</h2>
        <p>Dear {{ $reservation->guest->name ?? 'Guest' }},</p>
        <p>Thank you for choosing <strong>{{ $reservation->hotel->name ?? 'our hotel' }}</strong>.</p>
        <p>Your booking request has been received and is currently pending approval by the hotel administration. You will receive another email once your booking is confirmed.</p>
        
        <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0;"><strong>PNR:</strong> {{ $reservation->pnr }}</p>
            <p style="margin: 0;"><strong>Check-In:</strong> {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d M Y') }}</p>
            <p style="margin: 0;"><strong>Check-Out:</strong> {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d M Y') }}</p>
            <p style="margin: 0;"><strong>Rooms:</strong> {{ $reservation->rooms->count() }}</p>
        </div>

        <p>You can track your booking status using your PNR on our website.</p>
        <p>Best regards,<br>{{ $reservation->hotel->name ?? 'Merahkie Hotels' }}</p>
    </div>
</body>
</html>
