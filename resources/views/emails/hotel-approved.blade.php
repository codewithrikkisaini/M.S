<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Hotel Has Been Approved</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155; background-color: #f8fafc; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        
        <div style="border-bottom: 2px solid #10b981; padding-bottom: 16px; margin-bottom: 24px; text-align: center;">
            <h2 style="color: #059669; margin: 0; font-size: 24px; font-weight: 800;">🎉 Congratulations!</h2>
            <p style="color: #64748b; font-size: 15px; margin-top: 6px; margin-bottom: 0;">Your hotel has been successfully approved..</p>
        </div>

        <p style="font-size: 15px; color: #1e293b; line-height: 1.6;">
            Dear <strong>{{ $adminUser->name ?? $hotel->owner_name ?? 'Hotel Owner' }}</strong>,
        </p>

        <p style="font-size: 14px; color: #475569; line-height: 1.6;">
           You can now log in to your dashboard to:
        </p>

        <ul style="font-size: 14px; color: #334155; line-height: 1.8; margin-bottom: 24px;">
            <li>🏨 <strong>Add rooms</strong></li>
            <li>📅 <strong>View bookings</strong></li>
            <li>⚙️ <strong>Manage Hotel,pricing, etc.</strong></li>
        </ul>

        <div style="background-color: #f0fdf4; border-radius: 10px; padding: 20px; margin: 20px 0; border: 1px solid #bbf7d0;">
            <h3 style="margin-top: 0; color: #166534; font-size: 15px; margin-bottom: 12px;">Login Credentials Summary</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 4px 0; color: #166534; width: 140px;"><strong>Hotel Name:</strong></td>
                    <td style="padding: 4px 0; color: #0f172a;"><strong>{{ $hotel->name }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #166534;"><strong>Admin Email:</strong></td>
                    <td style="padding: 4px 0; color: #0f172a;">{{ $adminUser->email ?? $hotel->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #166534;"><strong>Status:</strong></td>
                    <td style="padding: 4px 0;"><span style="background: #dcfce7; color: #15803d; padding: 2px 10px; border-radius: 9999px; font-weight: bold; font-size: 12px;">APPROVED & ACTIVE</span></td>
                </tr>
            </table>
        </div>

        <div style="text-align: center; margin: 28px 0 16px 0;">
            <a href="{{ url('/login') }}" style="background-color: #10b981; color: #ffffff; padding: 12px 32px; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 14px; display: inline-block; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.25);">Login to Hotel Dashboard</a>
        </div>

        <p style="font-size: 14px; color: #475569; margin-top: 24px;">Thank you for choosing Lodgiko.</p>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 28px 0 16px 0;">
        <p style="font-size: 11px; color: #94a3b8; text-align: center; margin: 0;">Lodgiko SaaS Hotel System &bull; Official Approval Notification</p>
    </div>
</body>
</html>
