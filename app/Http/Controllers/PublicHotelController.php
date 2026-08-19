<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicHotelController extends Controller
{
    public function search(Request $request)
    {
        $validated = $request->validate([
            'check_in' => ['required', 'date_format:Y-m-d'],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
            'rooms' => ['nullable', 'integer', 'min:1'],
            'adults' => ['nullable', 'integer', 'min:1'],
            'children' => ['nullable', 'integer', 'min:0'],
            'hotel_id' => ['nullable', 'exists:hotels,id'],
            'city' => ['nullable', 'string', 'max:255'],
            'hotel' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Hotel::where('status', 'approved');

        if (!empty($validated['hotel_id'])) {
            $hotel = $query->where('id', $validated['hotel_id'])->first();
        } elseif (!empty($validated['city'])) {
            $hotel = $query->where('city', 'like', '%' . trim($validated['city']) . '%')->first();
        } elseif (!empty($validated['hotel'])) {
            $hotel = $query->where('name', 'like', '%' . trim($validated['hotel']) . '%')->first();
        } else {
            $hotel = $query->first();
        }

        if (!$hotel) {
            return back()->withErrors(['hotel' => 'No hotel matched your search.'])->withInput();
        }

        $params = [
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'rooms' => (int) ($validated['rooms'] ?? 1),
            'adults' => (int) ($validated['adults'] ?? 1),
            'children' => (int) ($validated['children'] ?? 0),
        ];

        if ($request->boolean('accessible_room')) {
            $params['accessible_room'] = 1;
        }

        if ($request->boolean('use_points')) {
            $params['use_points'] = 1;
        }

        if ($request->filled('special_rate')) {
            $params['special_rate'] = $request->special_rate;
        }

        return redirect()->route('hotel.show', array_merge(['slug' => $hotel->slug ?: $hotel->id], $params));
    }

    public function show(Request $request, $slug)
    {
        $checkInDate = Carbon::parse($request->query('check_in', old('check_in', date('Y-m-d'))))->format('Y-m-d');
        $checkOutDate = Carbon::parse($request->query('check_out', old('check_out', date('Y-m-d', strtotime('+1 day')))))->format('Y-m-d');

        if ($checkOutDate <= $checkInDate) {
            $checkOutDate = Carbon::parse($checkInDate)->addDay()->format('Y-m-d');
        }

        $adults = (int) $request->query('adults', old('adults', 1));
        $children = (int) $request->query('children', old('children', 0));
        $totalGuests = max(1, $adults + $children);
        $roomsCount = (int) $request->query('rooms', old('rooms', 1));

        $roomQuery = function ($q) use ($checkInDate, $checkOutDate) {
            $q->withoutGlobalScope('tenant')
              ->where('status', 'Available')
              ->whereDoesntHave('activeMaintenanceTickets')
              ->whereDoesntHave('housekeeping', function ($hk) {
                  $hk->whereIn('status', ['Dirty', 'Inspecting', 'Maintenance']);
              })
              ->whereDoesntHave('reservations', function ($res) use ($checkInDate, $checkOutDate) {
                  $res->whereIn('reservations.status', ['Confirmed', 'Checked-In', 'Pending'])
                      ->where('reservations.check_in_date', '<', $checkOutDate)
                      ->where('reservations.check_out_date', '>', $checkInDate);
              })
              ->with('roomType');
        };

        // 1. Load by exact slug match or ID
        $hotel = Hotel::where('slug', $slug)
            ->orWhere('id', $slug)
            ->with(['images', 'rooms' => $roomQuery])
            ->first();

        // 2. If not found, check if parameter is formatted like "hotall-hotall-9" or "emerald-grand-8"
        if (!$hotel) {
            $parts = explode('-', (string)$slug);
            $lastPart = end($parts);

            if (is_numeric($lastPart)) {
                $hotel = Hotel::where('id', $lastPart)
                    ->with(['images', 'rooms' => $roomQuery])
                    ->first();
            }
        }

        // 3. Fallback search by partial slug or name
        if (!$hotel) {
            $cleanName = str_replace('-', ' ', (string)$slug);
            $hotel = Hotel::where('name', 'LIKE', '%' . $cleanName . '%')
                ->with(['images', 'rooms' => $roomQuery])
                ->first();
        }

        if (!$hotel) {
            abort(404, 'Hotel not found');
        }

        $allRooms = Room::withoutGlobalScope('tenant')
            ->where('hotel_id', $hotel->id)
            ->with('roomType')
            ->get();

        return view('hotel.show', compact('hotel', 'allRooms', 'checkInDate', 'checkOutDate', 'adults', 'children', 'totalGuests', 'roomsCount'));
    }

    public function reserveRoom(Request $request, $slug, $roomId = null)
    {
        $roomQuery = function ($q) {
            $q->withoutGlobalScope('tenant')
              ->where('status', 'Available')
              ->whereDoesntHave('activeMaintenanceTickets')
              ->whereDoesntHave('housekeeping', function ($hk) {
                  $hk->whereIn('status', ['Dirty', 'Inspecting', 'Maintenance']);
              })
              ->whereDoesntHave('reservations', function ($res) {
                  $res->whereIn('reservations.status', ['Confirmed', 'Checked-In', 'Pending'])
                      ->where('check_out_date', '>=', date('Y-m-d'));
              })
              ->with('roomType');
        };

        $hotel = Hotel::where('slug', $slug)
            ->orWhere('id', $slug)
            ->with(['images', 'rooms' => $roomQuery])
            ->first();

        if (!$hotel) {
            $parts = explode('-', (string)$slug);
            $lastPart = end($parts);

            if (is_numeric($lastPart)) {
                $hotel = Hotel::where('id', $lastPart)
                    ->with(['images', 'rooms' => $roomQuery])
                    ->first();
            }
        }

        if (!$hotel) {
            $cleanName = str_replace('-', ' ', (string)$slug);
            $hotel = Hotel::where('name', 'LIKE', '%' . $cleanName . '%')
                ->with(['images', 'rooms' => $roomQuery])
                ->first();
        }

        if (!$hotel) {
            abort(404, 'Hotel not found');
        }

        $selectedRoom = null;

        if ($roomId) {
            $selectedRoom = Room::withoutGlobalScope('tenant')
                ->where('hotel_id', $hotel->id)
                ->where('id', $roomId)
                ->with('roomType')
                ->first();
        }

        if (!$selectedRoom) {
            $selectedRoom = Room::withoutGlobalScope('tenant')
                ->where('hotel_id', $hotel->id)
                ->where('status', '!=', 'Maintenance')
                ->whereDoesntHave('activeMaintenanceTickets')
                ->whereDoesntHave('housekeeping', function ($hk) {
                    $hk->whereIn('status', ['Dirty', 'Inspecting', 'Maintenance']);
                })
                ->with('roomType')
                ->first();
        }

        if (!$selectedRoom) {
            // Create fallback room instance for hotels with no rooms configured yet
            $selectedRoom = new Room([
                'id' => 0,
                'hotel_id' => $hotel->id,
                'room_number' => '101',
                'price' => 2500,
                'capacity' => 2,
                'bed_type' => 'King / Queen Bed',
                'description' => 'Deluxe Comfortable Room with modern amenities, Wi-Fi, and 24/7 service.'
            ]);
        }

        $checkin = $request->query('checkin', date('Y-m-d'));
        $checkout = $request->query('checkout', date('Y-m-d', strtotime('+1 day')));

        return view('hotel.reserve', compact('hotel', 'selectedRoom', 'checkin', 'checkout'));
    }

    public function bookInstant(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required',
            'room_id' => 'required',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:30',
        ]);

        $hotel = Hotel::find($request->hotel_id);
        $room = Room::withoutGlobalScope('tenant')->with('roomType')->find($request->room_id);

        if (!$hotel || !$room) {
            return response()->json(['success' => false, 'message' => 'Invalid Hotel or Room selection.'], 422);
        }

        $isUnderWork = $room->status === 'Maintenance' 
            || $room->activeMaintenanceTickets()->exists() 
            || $room->housekeeping()->whereIn('status', ['Dirty', 'Inspecting', 'Maintenance'])->exists();

        if ($isUnderWork || $room->status !== 'Available') {
            return response()->json(['success' => false, 'message' => 'Ye room abhi Maintenance ya Housekeeping process me hai! Kripya kisi clean/available room ko select karein.'], 422);
        }

        if ($hotel->account_status === 'suspended' || ($hotel->status !== 'approved' && $hotel->account_status !== 'active')) {
            return response()->json(['success' => false, 'message' => 'Online bookings for this hotel are currently paused or pending approval.'], 422);
        }

        // Blacklist check — prevent booking if guest identity matches active blacklist
        $blacklistService = app(\App\Services\GuestBlacklistService::class);
        $nameParts = explode(' ', trim($request->guest_name), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';
        $idNumber = $request->id_number ?? null;

        // Check existing guest first
        $existingGuest = Guest::withoutGlobalScope('tenant')->where('email', $request->guest_email)->first();
        if ($existingGuest) {
            $match = $blacklistService->isGuestBlacklisted($existingGuest);
            if ($match) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking Not Allowed: This guest is currently blacklisted and cannot make new reservations.',
                ], 422);
            }
        } else {
            // Check by identity for new guests
            $match = $blacklistService->isIdentityBlacklisted($firstName, $lastName, $idNumber);
            if ($match) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking Not Allowed: This guest identity is currently blacklisted and cannot make new reservations.',
                ], 422);
            }
        }

        $checkin = $request->checkin_date ?: date('Y-m-d');
        $checkout = $request->checkout_date ?: date('Y-m-d', strtotime('+1 day'));
        $days = max(1, (strtotime($checkout) - strtotime($checkin)) / 86400);
        $roomPrice = $room->price ?: ($room->roomType?->base_price ?? 2500);
        $totalPrice = $roomPrice * $days;

        $pnr = strtoupper(Str::random(6));
        $resId = null;

        DB::transaction(function () use ($request, $hotel, $room, $checkin, $checkout, $pnr, $roomPrice, $totalPrice, &$resId, &$guestObj) {
            // 1. Create or update guest
            $guestData = [
                'hotel_id'    => $hotel->id,
                'email'       => $request->guest_email,
                'name'        => $request->guest_name,
                'phone'       => $request->guest_phone,
                'nationality' => 'Indian',
            ];

            if ($request->filled('id_type')) {
                $guestData['id_type'] = $request->id_type;
            }
            if ($request->filled('id_number')) {
                $guestData['id_number'] = $request->id_number;
                $guestData['passport_number'] = $request->id_number;
            }

            $guest = Guest::withoutGlobalScope('tenant')->where('email', $request->guest_email)->first();
            if (!$guest) {
                $guestData['guest_id'] = 'G-' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT);
                $guest = Guest::create($guestData);
            } else {
                $guest->update($guestData);
            }

            $guestObj = $guest;

            // 2. Create Reservation
            $reservation = Reservation::create([
                'hotel_id' => $hotel->id,
                'guest_id' => $guest->id,
                'check_in_date' => $checkin,
                'check_out_date' => $checkout,
                'adults' => $request->guests_count ?: 1,
                'children' => 0,
                'discount_type' => 'Fixed',
                'discount_value' => 0,
                'tax_rate' => 18,
                'special_notes' => $request->special_requests,
                'status' => 'Pending',
                'pnr' => $pnr,
            ]);

            $resId = $reservation->id;
            $reservation->rooms()->attach($room->id, ['price' => $roomPrice]);
            $room->update(['status' => 'Occupied']);

            // 3. Payment: Only record payment if paid online (Card, UPI, Net Banking).
            // For Cash / Pay at Hotel, no payment record is created yet so balance remains due at check-in.
            if (in_array($request->payment_method, ['Card', 'UPI', 'Net Banking'])) {
                $paymentType = match ($request->payment_method) {
                    'Card', 'Net Banking' => 'Card',
                    'UPI' => 'UPI',
                    default => 'Cash',
                };

                Payment::create([
                    'hotel_id' => $hotel->id,
                    'reservation_id' => $reservation->id,
                    'amount' => $totalPrice,
                    'payment_type' => $paymentType,
                    'paid_at' => now(),
                ]);
            }

            // 4. Send Email Notification to Hotel
            try {
                if ($hotel->email) {
                    \Illuminate\Support\Facades\Mail::to($hotel->email)->send(new \App\Mail\BookingRequested($reservation));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send booking request email to hotel: ' . $e->getMessage());
            }

            // 5. Activity log
            ActivityLog::create([
                'hotel_id' => $hotel->id,
                'action' => 'Direct Booking Modal',
                'description' => "Instant modal booking requested for Guest: {$request->guest_name} (Room: {$room->room_number})",
                'ip_address' => request()->ip(),
            ]);
        });

        $paymentStatusText = in_array($request->payment_method, ['Card', 'UPI', 'Net Banking']) 
            ? ($request->payment_method . ' - Paid') 
            : 'Pay at Hotel (Pay on Check-in)';

        return response()->json([
            'success' => true,
            'pnr' => $pnr,
            'booking_number' => 'RES-' . $resId . '-' . date('Y'),
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,
            'id_type' => $guestObj->id_type ?? '—',
            'id_number' => $guestObj->id_number ?? $guestObj->passport_number ?? '—',
            'room_number' => $room->room_number,
            'room_type' => $room->roomType?->name ?: 'Standard Room',
            'hotel_name' => $hotel->name,
            'checkin_date' => date('d M Y', strtotime($checkin)),
            'checkout_date' => date('d M Y', strtotime($checkout)),
            'total_price' => number_format($totalPrice, 2),
            'payment_method' => $paymentStatusText,
            'booking_date' => date('d M Y, h:i A'),
        ]);
    }
}
