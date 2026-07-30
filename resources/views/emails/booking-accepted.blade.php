<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmed</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; background-color: #f4f6f8; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        <div style="background-color: #10b981; color: #ffffff; padding: 20px; text-align: center;">
            <h2 style="margin: 0; font-size: 20px;">Booking Confirmed! 🎉</h2>
            <p style="margin: 5px 0 0 0; font-size: 13px; opacity: 0.9;">Your reservation has been approved by the hotel</p>
        </div>

        <div style="padding: 24px;">
            <p style="margin-top: 0;">Dear {{ $reservation->guest->name ?? 'Guest' }},</p>
            <p>We are delighted to inform you that your booking request at <strong>{{ $reservation->hotel->name ?? 'our hotel' }}</strong> has been officially <strong>ACCEPTED & CONFIRMED</strong>!</p>
            
            <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #e2e8f0;">
                <h4 style="margin: 0 0 10px 0; color: #1e293b; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px;">Your Booking Summary</h4>
                <p style="margin: 4px 0;"><strong>PNR (Tracker):</strong> <span style="color: #10b981; font-weight: bold;">{{ $reservation->pnr }}</span></p>
                <p style="margin: 4px 0;"><strong>Booking Ref:</strong> RES-{{ $reservation->id }}</p>
                <p style="margin: 4px 0;"><strong>Check-In:</strong> {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d M Y') }} (From 02:00 PM)</p>
                <p style="margin: 4px 0;"><strong>Check-Out:</strong> {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d M Y') }} (Until 11:00 AM)</p>
                <p style="margin: 4px 0;"><strong>Confirmed Room(s):</strong> 
                    @if($reservation->rooms && $reservation->rooms->count() > 0)
                        {{ $reservation->rooms->map(fn($r) => 'Room ' . $r->room_number . ' (' . ($r->roomType->name ?? 'Standard') . ')')->join(', ') }}
                    @else
                        Assigned at Check-In
                    @endif
                </p>
            </div>

            <p style="font-size: 13px; color: #64748b;">We look forward to hosting you! If you have any questions or special requests, please contact the hotel directly.</p>
        </div>

        <div style="background-color: #f1f5f9; padding: 15px; text-align: center; font-size: 12px; color: #64748b;">
            Warm regards,<br><strong>{{ $reservation->hotel->name ?? 'Hotel Management' }}</strong>
        </div>
    </div>
</body>
</html>
