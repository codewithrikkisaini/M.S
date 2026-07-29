<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Hotel Registration Alert</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155; background-color: #f8fafc; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        
        <div style="border-bottom: 2px solid #4f46e5; padding-bottom: 16px; margin-bottom: 24px;">
            <h2 style="color: #4f46e5; margin: 0; font-size: 22px; font-weight: 700;">🏨 New Hotel Registration Alert</h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 6px; margin-bottom: 0;">A new hotel property has registered on the platform and requires Super Admin review.</p>
        </div>

        <div style="background-color: #f1f5f9; border-radius: 10px; padding: 18px; margin-bottom: 20px;">
            <h3 style="margin-top: 0; color: #0f172a; font-size: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Hotel Property Details</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 4px 0; color: #64748b; width: 140px;"><strong>Hotel Name:</strong></td>
                    <td style="padding: 4px 0; color: #0f172a;"><strong>{{ $hotel->name }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #64748b;"><strong>Business Name:</strong></td>
                    <td style="padding: 4px 0; color: #0f172a;">{{ $hotel->business_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #64748b;"><strong>Hotel Email:</strong></td>
                    <td style="padding: 4px 0; color: #0f172a;">{{ $hotel->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #64748b;"><strong>Phone:</strong></td>
                    <td style="padding: 4px 0; color: #0f172a;">{{ $hotel->phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #64748b;"><strong>City / Country:</strong></td>
                    <td style="padding: 4px 0; color: #0f172a;">{{ $hotel->city ?? 'N/A' }}, {{ $hotel->country }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #64748b;"><strong>Total Rooms:</strong></td>
                    <td style="padding: 4px 0; color: #0f172a;">{{ $hotel->rooms_count }} Rooms</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #64748b;"><strong>Category / Type:</strong></td>
                    <td style="padding: 4px 0; color: #0f172a;">{{ $hotel->category }} - {{ $hotel->property_type }}</td>
                </tr>
            </table>
        </div>

        <div style="background-color: #eef2ff; border-radius: 10px; padding: 18px; margin-bottom: 24px; border-left: 4px solid #4f46e5;">
            <h3 style="margin-top: 0; color: #3730a3; font-size: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Registered Administrator</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 4px 0; color: #475569; width: 140px;"><strong>Admin Name:</strong></td>
                    <td style="padding: 4px 0; color: #1e1b4b;"><strong>{{ $adminUser->name }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #475569;"><strong>Admin Email:</strong></td>
                    <td style="padding: 4px 0; color: #1e1b4b;">{{ $adminUser->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #475569;"><strong>Status:</strong></td>
                    <td style="padding: 4px 0;"><span style="background: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 12px;">Pending Approval</span></td>
                </tr>
            </table>
        </div>

        <p style="font-size: 14px; color: #475569; line-height: 1.5;">Please log in to your Super Admin dashboard to inspect and approve this hotel application.</p>
        
        <div style="text-align: center; margin-top: 24px; margin-bottom: 12px;">
            <a href="{{ url('/superadmin/hotels') }}" style="background-color: #4f46e5; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-block; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3);">Review in SuperAdmin Panel</a>
        </div>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0 16px 0;">
        <p style="font-size: 11px; color: #94a3b8; text-align: center; margin: 0;">Merahkie SaaS Hotel System &bull; Automated Super Admin Alert</p>
    </div>
</body>
</html>
