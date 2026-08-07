<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Room extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = ['room_number', 'room_type_id', 'price', 'status', 'floor', 'description', 'hotel_id', 'image_path'];

    public function getImagesAttribute(): array
    {
        if (!empty($this->image_path)) {
            $decoded = json_decode($this->image_path, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return array_map(function ($img) {
                    return filter_var($img, FILTER_VALIDATE_URL) ? $img : asset('storage/' . $img);
                }, $decoded);
            }

            if (str_contains($this->image_path, ',')) {
                $items = array_filter(array_map('trim', explode(',', $this->image_path)));
                return array_map(function ($img) {
                    return filter_var($img, FILTER_VALIDATE_URL) ? $img : asset('storage/' . $img);
                }, array_values($items));
            }

            return [filter_var($this->image_path, FILTER_VALIDATE_URL) ? $this->image_path : asset('storage/' . $this->image_path)];
        }

        $fallbacks = [
            'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=1000&q=80'
        ];

        $offset = abs(crc32($this->room_number ?? (string)$this->id));
        return [
            $fallbacks[$offset % count($fallbacks)],
            $fallbacks[($offset + 1) % count($fallbacks)],
            $fallbacks[($offset + 2) % count($fallbacks)],
        ];
    }

    public function getImageUrlAttribute(): string
    {
        $all = $this->images;
        return $all[0] ?? 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=800&q=80';
    }

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
