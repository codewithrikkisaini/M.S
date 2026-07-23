<?php

namespace App\Services;

use App\Repositories\ReservationRepositoryInterface;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    protected $reservationRepository;

    public function __construct(ReservationRepositoryInterface $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    public function getPaginatedReservations($search)
    {
        return $this->reservationRepository->getAllPaginated($search);
    }

    public function saveReservation($id, $data, $isEditMode)
    {
        return DB::transaction(function () use ($id, $data, $isEditMode) {
            $roomIds = $data['room_ids'];
            unset($data['room_ids']);

            $reservation = $this->reservationRepository->createOrUpdate($id, $data);

            $nights = 1;
            if (!empty($data['check_in_date']) && !empty($data['check_out_date'])) {
                $nights = max(1, (int) ceil(\Carbon\Carbon::parse($data['check_in_date'])->diffInDays(\Carbon\Carbon::parse($data['check_out_date']))));
            }

            $pivotData = [];
            foreach (Room::with('roomType')->whereIn('id', $roomIds)->get() as $room) {
                $rate = (float) ($room->price ?? 0);
                if ($room->roomType) {
                    if ($nights >= 30 && (float)$room->roomType->monthly_rate > 0) {
                        $rate = round((float)$room->roomType->monthly_rate / 30, 2);
                    } elseif ($nights >= 7 && (float)$room->roomType->weekly_rate > 0) {
                        $rate = round((float)$room->roomType->weekly_rate / 7, 2);
                    } elseif ((float)$room->roomType->daily_rate > 0) {
                        $rate = (float) $room->roomType->daily_rate;
                    }
                }
                $pivotData[$room->id] = ['price' => $rate];

                if (!$isEditMode && $room->status === 'Available') {
                    $room->update(['status' => 'Reserved']);
                }
            }
            $reservation->rooms()->sync($pivotData);

            return $reservation;
        });
    }

    public function deleteReservation($id)
    {
        return DB::transaction(function () use ($id) {
            $res = $this->reservationRepository->findById($id);
            if ($res->status != 'Checked-Out' && $res->status != 'Cancelled') {
                foreach ($res->rooms as $room) {
                    $room->update(['status' => 'Available']);
                }
            }
            return $this->reservationRepository->delete($id);
        });
    }

    public function getCalendarEvents($start, $end)
    {
        return $this->reservationRepository->getEventsByDateRange($start, $end);
    }

    public function processCheckIn($reservationId, $userId, $remarks = null)
    {
        return DB::transaction(function () use ($reservationId, $userId, $remarks) {
            $res = $this->reservationRepository->findById($reservationId);
            if ($res->status !== 'Confirmed' && $res->status !== 'Reserved') {
                throw new \Exception('Reservation must be Confirmed to Check-In.');
            }

            // Change Reservation Status
            $res->update(['status' => 'Checked-In']);

            // Change Room Status for every room in this booking
            foreach ($res->rooms as $room) {
                $room->update(['status' => 'Occupied']);
            }

            // Generate Booking Code MK2026001
            $bookingCode = 'MK' . date('Y') . str_pad($reservationId, 3, '0', STR_PAD_LEFT);

            // Create CheckIn record
            return \App\Models\CheckIn::create([
                'reservation_id' => $reservationId,
                'booking_code' => $bookingCode,
                'checkin_datetime' => now(),
                'user_id' => $userId,
                'remarks' => $remarks
            ]);
        });
    }

    public function processCheckOut($reservationId)
    {
        return DB::transaction(function () use ($reservationId) {
            $res = $this->reservationRepository->findById($reservationId);
            if ($res->status !== 'Checked-In') {
                throw new \Exception('Reservation must be Checked-In to Check-Out.');
            }

            $checkIn = \App\Models\CheckIn::where('reservation_id', $reservationId)->first();
            if (!$checkIn) {
                throw new \Exception('No Check-In record found for this reservation.');
            }

            // Calculate nights actually stayed (a partial day counts as a full night)
            $checkInDate = \Carbon\Carbon::parse($res->check_in_date);
            $checkOutDate = now();
            $nights = max(1, (int) ceil($checkInDate->diffInDays($checkOutDate)));

            // Calculate charges (rooms + discount + tax)
            $charges = $res->calculateCharges($nights);

            $totalPaid = $res->total_paid;
            if ($totalPaid < $charges['total']) {
                $remaining = round($charges['total'] - $totalPaid, 2);
                throw new \Exception("Guest still owes \${$remaining}. Please collect the remaining payment before check-out.");
            }

            // Change Reservation Status
            $res->update(['status' => 'Checked-Out']); // 'Completed' conceptually

            // Change Room Status for every room in this booking and trigger Dirty status in housekeeping
            foreach ($res->rooms as $room) {
                $room->update(['status' => 'Available']);

                \App\Models\Housekeeping::create([
                    'room_id' => $room->id,
                    'status' => 'Dirty',
                    'updated_by' => $res->checkIn->user_id ?? 1,
                    'notes' => 'Auto-created on guest check-out of Reservation #' . $res->id
                ]);
            }

            // Create CheckOut record
            $checkout = \App\Models\CheckOut::create([
                'reservation_id' => $reservationId,
                'checkout_datetime' => $checkOutDate,
                'nights' => $nights,
                'subtotal' => $charges['subtotal'],
                'discount' => $charges['discount'],
                'tax' => $charges['tax'],
                'tax_rate' => $charges['tax_rate'],
                'total_amount' => $charges['total']
            ]);

            // Create Invoice
            \App\Models\Invoice::create([
                'invoice_number' => 'MK-INV-' . date('Y') . '-' . str_pad($checkout->id, 3, '0', STR_PAD_LEFT),
                'checkout_id' => $checkout->id
            ]);

            return $checkout;
        });
    }
}
