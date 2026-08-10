<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use App\Mail\AdminNewHotelNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class HotelRegistrationController extends Controller
{
    public function showForm()
    {
        return view('auth.register-hotel');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'hotel_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'unique:hotels,email'],
            'phone' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'property_type' => ['nullable', 'string', 'max:100'],
            'rooms_count' => ['nullable', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            // 1. Create Hotel with pending status & auto hotel_code
            $hotelCode = Hotel::generateNextHotelCode();

            $hotel = Hotel::create([
                'name' => $validated['hotel_name'],
                'hotel_code' => $hotelCode,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'owner_name' => $validated['owner_name'],
                'country' => $validated['country'],
                'city' => $validated['city'],
                'address' => $validated['address'] ?? null,
                'property_type' => $validated['property_type'] ?? 'Hotel',
                'rooms_count' => $validated['rooms_count'] ?? 10,
                'status' => 'pending',
                'account_status' => 'pending_approval',
                'currency' => 'USD',
                'timezone' => 'UTC',
            ]);

            // 2. Create Admin User for Hotel
            $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);

            $user = User::create([
                'name' => $validated['owner_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $adminRole->id,
                'hotel_id' => $hotel->id,
                'status' => 'active',
            ]);

            // 3. Log Audit Activity
            ActivityLog::create([
                'hotel_id' => $hotel->id,
                'user_id' => $user->id,
                'action' => 'Hotel Registration',
                'description' => "Hotel registered: {$hotel->name} ({$hotel->hotel_code}). Initial status: Pending Approval",
                'ip_address' => $request->ip(),
            ]);

            // 4. Send Email Notification to Super Admin
            $superAdmins = User::whereHas('role', function ($q) {
                $q->where('slug', 'superadmin');
            })->get();

            $recipientEmail = config('mail.admin_address', 'rikkisaini4455@gmail.com');

            try {
                Mail::to($recipientEmail)->send(new AdminNewHotelNotificationMail($hotel));
                foreach ($superAdmins as $admin) {
                    if ($admin->email !== $recipientEmail) {
                        Mail::to($admin->email)->send(new AdminNewHotelNotificationMail($hotel));
                    }
                }
            } catch (\Exception $e) {
                logger()->error('Failed sending AdminNewHotelNotificationMail: ' . $e->getMessage());
            }

            return redirect()->route('register-hotel.success', ['code' => $hotel->hotel_code]);
        });
    }

    public function success(Request $request)
    {
        $code = $request->query('code');
        $hotel = Hotel::where('hotel_code', $code)->first();

        return view('auth.register-success', compact('hotel', 'code'));
    }
}
