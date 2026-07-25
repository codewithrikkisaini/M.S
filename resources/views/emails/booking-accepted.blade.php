<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmed</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-w-2xl mx-auto p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        <h2 style="color: #10b981;">Booking Confirmed!</h2>
        <p>Dear {{ $reservation->guest->name ?? 'Guest' }},</p>
        <p>We are delighted to inform you that your booking at <strong>{{ $reservation->hotel->name ?? 'our hotel' }}</strong> has been officially <strong>confirmed</strong>!</p>
        
        <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0;"><strong>PNR:</strong> {{ $reservation->pnr }}</p>
            <p style="margin: 0;"><strong>Booking Reference:</strong> RES-{{ $reservation->id }}</p>
            <p style="margin: 0;"><strong>Check-In:</strong> {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d M Y') }} (From 02:00 PM)</p>
            <p style="margin: 0;"><strong>Check-Out:</strong> {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d M Y') }} (Until 11:00 AM)</p>
        </div>

        <p>We look forward to hosting you. If you have any special requests, please let us know.</p>
        <p>Best regards,<br>{{ $reservation->hotel->name ?? 'Merahkie Hotels' }}</p>
    </div>
</body>
</html>
