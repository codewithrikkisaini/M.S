<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Hotel Registration Request</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155; background-color: #f8fafc; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        
        <div style="border-bottom: 2px solid #4f46e5; padding-bottom: 16px; margin-bottom: 24px;">
            <h2 style="color: #4f46e5; margin: 0; font-size: 22px; font-weight: 700;">🏨 New Hotel Registration Request</h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 6px; margin-bottom: 0;">Ek naya hotel registration request aayi hai. Admin panel me login karke approve ya reject karein.</p>
        </div>

        <div style="background-color: #f1f5f9; border-radius: 10px; padding: 18px; margin-bottom: 20px;">
            <h3 style="margin-top: 0; color: #0f172a; font-size: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Registration Details</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 6px 0; color: #64748b; width: 150px;"><strong>Hotel Name:</strong></td>
                    <td style="padding: 6px 0; color: #0f172a;"><strong>{{ $hotel->name }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b;"><strong>Owner Name:</strong></td>
                    <td style="padding: 6px 0; color: #0f172a;"><strong>{{ $adminUser->name ?? $hotel->owner_name ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b;"><strong>Owner Email:</strong></td>
                    <td style="padding: 6px 0; color: #0f172a;">{{ $adminUser->email ?? $hotel->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b;"><strong>Phone Number:</strong></td>
                    <td style="padding: 6px 0; color: #0f172a;">{{ $hotel->phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b;"><strong>City:</strong></td>
                    <td style="padding: 6px 0; color: #0f172a;">{{ $hotel->city ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b;"><strong>Registration Date:</strong></td>
                    <td style="padding: 6px 0; color: #0f172a;">{{ $hotel->created_at ? $hotel->created_at->format('d M Y, h:i A') : date('d M Y') }}</td>
                </tr>
            </table>
        </div>

        <p style="font-size: 14px; color: #475569; line-height: 1.5;">Kripya Super Admin panel me login karke is application ko review karein aur Approve ya Reject karein.</p>
        
        <div style="text-align: center; margin-top: 24px; margin-bottom: 12px;">
            <a href="{{ url('/superadmin/hotels') }}" style="background-color: #4f46e5; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-block; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3);">Open SuperAdmin Panel</a>
        </div>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0 16px 0;">
        <p style="font-size: 11px; color: #94a3b8; text-align: center; margin: 0;">Lodgiko SaaS Hotel System &bull; Automated Super Admin Alert</p>
    </div>
</body>
</html>
