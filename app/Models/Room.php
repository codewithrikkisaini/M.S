<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['room_number', 'room_type_id', 'price', 'status', 'floor'];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_rooms')->withPivot('price')->withTimestamps();
    }

    public function housekeeping()
    {
        return $this->hasMany(Housekeeping::class);
    }

    public function latestHousekeeping()
    {
        return $this->hasOne(Housekeeping::class)->latestOfMany();
    }

    public function maintenanceTickets()
    {
        return $this->hasMany(MaintenanceTicket::class);
    }

    public function activeMaintenanceTickets()
    {
        return $this->hasMany(MaintenanceTicket::class)->whereIn('status', ['Open', 'In Progress']);
    }

    public function scopeAvailableBetween($query, $checkIn, $checkOut, $excludeReservationId = null)
    {
        return $query->where('rooms.status', '!=', 'Maintenance')
            ->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut, $excludeReservationId) {
                $q->whereIn('reservations.status', ['Confirmed', 'Checked-In'])
                    ->where('reservations.check_in_date', '<', $checkOut)
                    ->where('reservations.check_out_date', '>', $checkIn);

                if ($excludeReservationId) {
                    $q->where('reservations.id', '!=', $excludeReservationId);
                }
            });
    }
}
