<?php

namespace App\Repositories;

use App\Models\Reservation;

class ReservationRepository implements ReservationRepositoryInterface
{
    public function getAllPaginated($search, $perPage = 10)
    {
        return Reservation::with(['guest', 'rooms', 'payments'])
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('guest', function($gq) use ($search) {
                        $gq->where('name', 'like', '%' . $search . '%')
                           ->orWhere('email', 'like', '%' . $search . '%')
                           ->orWhere('phone', 'like', '%' . $search . '%')
                           ->orWhere('id_type', 'like', '%' . $search . '%')
                           ->orWhere('id_number', 'like', '%' . $search . '%')
                           ->orWhere('passport_number', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('rooms', function($rq) use ($search) {
                        $rq->where('room_number', 'like', '%' . $search . '%');
                    })
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhere('booking_type', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function findById($id)
    {
        return Reservation::findOrFail($id);
    }

    public function createOrUpdate($id, array $data)
    {
        return Reservation::updateOrCreate(['id' => $id], $data);
    }

    public function delete($id)
    {
        return Reservation::destroy($id);
    }

    public function getEventsByDateRange($start, $end)
    {
        return Reservation::with(['guest', 'rooms'])
            ->whereBetween('check_in_date', [$start, $end])
            ->orWhereBetween('check_out_date', [$start, $end])
            ->get();
    }
}
