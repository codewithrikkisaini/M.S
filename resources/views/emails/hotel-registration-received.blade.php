<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registration Received</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155; background-color: #f8fafc; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        
        <div style="border-bottom: 2px solid #10b981; padding-bottom: 16px; margin-bottom: 24px;">
            <h2 style="color: #059669; margin: 0; font-size: 22px; font-weight: 700;">🎉 Registration Application Received!</h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 6px; margin-bottom: 0;">Thank you for registering <strong>{{ $hotel->name }}</strong> with us.</p>
        </div>

        <p style="font-size: 15px; color: #1e293b; line-height: 1.6;">Dear <strong>{{ $adminUser->name }}</strong>,</p>

        <p style="font-size: 14px; color: #475569; line-height: 1.6;">
            We have successfully received your hotel registration request for <strong>{{ $hotel->name }}</strong>. Our Super Admin team is currently reviewing your property details.
        </p>

        <div style="background-color: #f0fdf4; border-radius: 10px; padding: 18px; margin: 20px 0; border: 1px solid #bbf7d0;">
            <h3 style="margin-top: 0; color: #166534; font-size: 15px;">Application Summary</h3>
            <p style="margin: 4px 0; font-size: 14px;"><strong>Hotel Name:</strong> {{ $hotel->name }}</p>
            <p style="margin: 4px 0; font-size: 14px;"><strong>Admin Email:</strong> {{ $adminUser->email }}</p>
            <p style="margin: 4px 0; font-size: 14px;"><strong>Rooms Configured:</strong> {{ $hotel->rooms_count }}</p>
            <p style="margin: 4px 0; font-size: 14px;"><strong>Current Status:</strong> <span style="color: #d97706; font-weight: bold;">Under Review</span></p>
        </div>

        <p style="font-size: 14px; color: #475569; line-height: 1.6;">
            Once your property is approved by our team, you will receive an approval email notification and can instantly log into your dedicated Hotel Admin Dashboard.
        </p>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0 16px 0;">
        <p style="font-size: 11px; color: #94a3b8; text-align: center; margin: 0;">LODGIKO Hotel Management System &bull; All rights reserved</p>
    </div>
</body>
</html>
