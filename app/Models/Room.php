<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = ['room_number', 'room_type_id', 'price', 'capacity', 'status', 'floor', 'bed_type', 'room_option', 'description', 'hotel_id', 'image_path'];


    public function getCapacityAttribute($value): int
    {
        if (!empty($value) && (int)$value > 0) {
            return (int) $value;
        }

        $bed = strtolower($this->bed_type ?? '');
        $option = strtolower($this->room_option ?? '');
        $type = strtolower($this->roomType?->name ?? '');

        if (str_contains($bed, 'single') || str_contains($type, 'single')) {
            return 1;
        }
        if (str_contains($bed, '2 double') || str_contains($bed, 'bunk') || str_contains($bed, 'family') || str_contains($type, 'family') || str_contains($type, 'suite')) {
            return 4;
        }
        if (str_contains($bed, 'extra') || str_contains($option, 'extra guest') || str_contains($bed, 'triple')) {
            return 3;
        }
        if (str_contains($type, 'executive') || str_contains($type, 'apartment') || str_contains($bed, 'california')) {
            return 5;
        }
        if (str_contains($type, 'presidential') || str_contains($type, 'grand') || str_contains($type, 'villa')) {
            return 6;
        }

        return 2;
    }

    public static function formatUrl(string $img): string
    {
        $img = trim($img);
        if (empty($img)) {
            return '';
        }

        if (filter_var($img, FILTER_VALIDATE_URL) || \Illuminate\Support\Str::startsWith($img, ['http://', 'https://'])) {
            if (preg_match('/https?:\/\/localhost(:\d+)?\/(storage\/)?(.*)/i', $img, $matches)) {
                $img = ltrim($matches[3], '/');
            } else {
                return $img;
            }
        }

        $clean = preg_replace('/^\/?(storage\/|public\/)+/', '', $img);
        $clean = ltrim($clean, '/');

        if (empty($clean)) {
            return '';
        }

        return '/storage/' . $clean;
    }

    public function getImagesAttribute(): array
    {
        if (!empty($this->image_path)) {
            $raw = $this->image_path;
            $items = [];

            $decoded = json_decode($raw, true);
            if (is_array($decoded) && count($decoded) > 0) {
                $items = $decoded;
            } elseif (str_contains($raw, ',')) {
                $items = array_filter(array_map('trim', explode(',', $raw)));
            } else {
                $items = [$raw];
            }

            $formatted = [];
            foreach ($items as $img) {
                $u = self::formatUrl((string) $img);
                if (!empty($u)) {
                    $formatted[] = $u;
                }
            }

            if (count($formatted) > 0) {
                return array_values($formatted);
            }
        }

        $fallbacks = [
            'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=1000&q=80'
        ];

        $offset = abs(crc32($this->room_number ?? (string) $this->id));
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
