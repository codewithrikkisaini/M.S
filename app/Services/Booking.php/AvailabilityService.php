<?php

namespace App\Services\Booking;

use App\Models\Hotel;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    public function search(
        int $hotelId,
        string $checkIn,
        string $checkOut,
        int $adults = 1,
        int $children = 0,
        int $roomsRequested = 1
    ): Collection {
        $checkInDate = Carbon::parse($checkIn)->startOfDay();
        $checkOutDate = Carbon::parse($checkOut)->startOfDay();

        if ($checkOutDate->lessThanOrEqualTo($checkInDate)) {
            throw new \InvalidArgumentException(
                'Check-out date must be after check-in date.'
            );
        }

        $hotel = Hotel::findOrFail($hotelId);

        $roomTypes = RoomType::query()
            ->where('hotel_id', $hotel->id)
            ->where('status', 'active')
            ->with([
                'rooms' => function ($query) {
                    $query->where('status', '!=', 'Maintenance');
                }
            ])
            ->get();

        return $roomTypes
            ->map(function ($roomType) use (
                $checkInDate,
                $checkOutDate,
                $adults,
                $children,
                $roomsRequested
            ) {
                $rooms = $roomType->rooms;

                /*
                 * Find rooms already occupied by active reservations.
                 */
                $occupiedRoomIds = \DB::table('reservation_rooms')
                    ->join(
                        'reservations',
                        'reservations.id',
                        '=',
                        'reservation_rooms.reservation_id'
                    )
                    ->whereIn('reservations.status', [
                        'Confirmed',
                        'Checked-In',
                    ])
                    ->where(
                        'reservations.check_in_date',
                        '<',
                        $checkOutDate->toDateString()
                    )
                    ->where(
                        'reservations.check_out_date',
                        '>',
                        $checkInDate->toDateString()
                    )
                    ->pluck('reservation_rooms.room_id');

                $availableRooms = $rooms->filter(function ($room) use (
                    $occupiedRoomIds
                ) {
                    return !$occupiedRoomIds->contains($room->id);
                });

                $availableCount = $availableRooms->count();

                /*
                 * Determine nightly rate.
                 */
                $nights = $checkInDate->diffInDays($checkOutDate);

                $dailyRate = (float) $roomType->daily_rate;

                if ($nights >= 30 && (float) $roomType->monthly_rate > 0) {
                    $nightlyRate = round(
                        (float) $roomType->monthly_rate / 30,
                        2
                    );
                } elseif ($nights >= 7 && (float) $roomType->weekly_rate > 0) {
                    $nightlyRate = round(
                        (float) $roomType->weekly_rate / 7,
                        2
                    );
                } else {
                    $nightlyRate = $dailyRate;
                }

                $subtotal = round(
                    $nightlyRate * $nights * $roomsRequested,
                    2
                );

                $tax = round(
                    $subtotal * ((float) $roomType->tax_percent / 100),
                    2
                );

                $total = round(
                    $subtotal + $tax,
                    2
                );

                return [
                    'room_type_id' => $roomType->id,
                    'name' => $roomType->name,

                    'available_rooms' => $availableCount,

                    'nightly_rate' => $nightlyRate,

                    'nights' => $nights,

                    'subtotal' => $subtotal,

                    'tax_percent' => (float) $roomType->tax_percent,

                    'tax' => $tax,

                    'total' => $total,

                    'can_book' => $availableCount >= $roomsRequested,
                ];
            })
            ->filter(function ($roomType) {
                return $roomType['can_book'];
            })
            ->values();
    }
}