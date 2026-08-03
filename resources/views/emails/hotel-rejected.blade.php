<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hotel Registration Update</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155; background-color: #f8fafc; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        
        <div style="border-bottom: 2px solid #ef4444; padding-bottom: 16px; margin-bottom: 24px; text-align: center;">
            <h2 style="color: #dc2626; margin: 0; font-size: 22px; font-weight: 700;">Hotel Registration Update</h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 6px; margin-bottom: 0;">Status update regarding your registration for <strong>{{ $hotel->name }}</strong></p>
        </div>

        <p style="font-size: 15px; color: #1e293b; line-height: 1.6;">
            Dear <strong>{{ $adminUser->name ?? $hotel->owner_name ?? 'Hotel Owner' }}</strong>,
        </p>

        <p style="font-size: 14px; color: #475569; line-height: 1.6;">
            We regret to inform you that your hotel registration request for <strong>{{ $hotel->name }}</strong> could not be approved at this time.
        </p>

        <div style="background-color: #fef2f2; border-radius: 10px; padding: 18px; margin: 20px 0; border: 1px solid #fecaca; font-size: 14px; color: #991b1b; line-height: 1.6;">
            Agar zarurat ho to please apni property details ya business documents update karke dubara registration request submit karein ya Support team se contact karein.
        </div>

        <p style="font-size: 14px; color: #475569; line-height: 1.6;">
            Thank you for your interest in Lodgiko.
        </p>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 28px 0 16px 0;">
        <p style="font-size: 11px; color: #94a3b8; text-align: center; margin: 0;">Lodgiko SaaS Hotel System &bull; Registration Department</p>
    </div>
</body>
</html>
