<!DOCTYPE html>
<html>
<head>
    <title>New Booking Request Notification</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; background-color: #f4f6f8; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        <div style="background-color: #2563eb; color: #ffffff; padding: 20px; text-align: center;">
            <h2 style="margin: 0; font-size: 20px;">New Booking Request Received</h2>
            <p style="margin: 5px 0 0 0; font-size: 13px; opacity: 0.9;">Action Required: Please Accept/Confirm in Hotel Dashboard</p>
        </div>

        <div style="padding: 24px;">
            <p style="margin-top: 0;">Dear Hotel Management,</p>
            <p>A new room booking request has been submitted for <strong>{{ $reservation->hotel->name ?? 'your property' }}</strong>. Below are the guest and room details:</p>
            
            {{-- Guest Details --}}
            <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; margin: 15px 0; border: 1px solid #e2e8f0;">
                <h4 style="margin: 0 0 10px 0; color: #1e293b; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px;">Guest Details</h4>
                <p style="margin: 4px 0;"><strong>Guest Name:</strong> {{ $reservation->guest->name ?? 'N/A' }}</p>
                <p style="margin: 4px 0;"><strong>Guest Email:</strong> {{ $reservation->guest->email ?? 'N/A' }}</p>
                <p style="margin: 4px 0;"><strong>Guest Phone:</strong> {{ $reservation->guest->phone ?? 'N/A' }}</p>
            </div>

            {{-- Booking & Room Details --}}
            <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; margin: 15px 0; border: 1px solid #e2e8f0;">
                <h4 style="margin: 0 0 10px 0; color: #1e293b; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px;">Reservation & Room Info</h4>
                <p style="margin: 4px 0;"><strong>PNR:</strong> <span style="color: #2563eb; font-weight: bold;">{{ $reservation->pnr }}</span></p>
                <p style="margin: 4px 0;"><strong>Check-In Date:</strong> {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d M Y') }}</p>
                <p style="margin: 4px 0;"><strong>Check-Out Date:</strong> {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d M Y') }}</p>
                <p style="margin: 4px 0;"><strong>Booked Room(s):</strong> 
                    @if($reservation->rooms && $reservation->rooms->count() > 0)
                        {{ $reservation->rooms->map(fn($r) => 'Room ' . $r->room_number . ' (' . ($r->roomType->name ?? 'Standard') . ')')->join(', ') }}
                    @else
                        Standard Room
                    @endif
                </p>
                <p style="margin: 4px 0;"><strong>Total Rooms:</strong> {{ $reservation->rooms->count() ?: 1 }}</p>
            </div>

            <p style="font-size: 13px; color: #64748b;">Please log in to your Hotel Dashboard to Review & Accept this booking request to notify the guest.</p>

            <div style="text-align: center; margin-top: 25px;">
                <a href="{{ url('/reservations') }}" style="background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; display: inline-block; font-size: 14px;">Open Dashboard Reservations</a>
            </div>
        </div>

        <div style="background-color: #f1f5f9; padding: 15px; text-align: center; font-size: 12px; color: #64748b;">
            Best regards,<br><strong>{{ $reservation->hotel->name ?? 'Hotel System' }}</strong>
        </div>
    </div>
</body>
</html>
