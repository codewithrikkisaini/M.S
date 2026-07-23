<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Reservation extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'guest_id', 'check_in_date',
        'check_out_date', 'adults', 'children',
        'discount_type', 'discount_value', 'tax_rate',
        'special_notes', 'status', 'hotel_id'
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'reservation_rooms')->withPivot('price')->withTimestamps();
    }

    public function checkIn()
    {
        return $this->hasOne(CheckIn::class);
    }

    public function checkOut()
    {
        return $this->hasOne(CheckOut::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getNightsAttribute(): int
    {
        $nights = \Carbon\Carbon::parse($this->check_in_date)->diffInDays(\Carbon\Carbon::parse($this->check_out_date));

        return max(1, (int) ceil($nights));
    }

    public function calculateCharges(?int $nights = null): array
    {
        $nights = $nights ?? $this->nights;

        $roomsPerNight = (float) $this->rooms->sum(function ($r) use ($nights) {
            $pivotPrice = (float) ($r->pivot?->price ?? 0);
            if ($pivotPrice > 0) {
                return $pivotPrice;
            }

            if ($r->roomType) {
                if ($nights >= 30 && $r->roomType->monthly_rate > 0) {
                    return round((float) $r->roomType->monthly_rate / 30, 2);
                }
                if ($nights >= 7 && $r->roomType->weekly_rate > 0) {
                    return round((float) $r->roomType->weekly_rate / 7, 2);
                }
                if ($r->roomType->daily_rate > 0) {
                    return (float) $r->roomType->daily_rate;
                }
            }

            return (float) ($r->price ?? 0);
        });

        $subtotal = round($nights * $roomsPerNight, 2);

        $discount = $this->discount_type === 'Percentage'
            ? round($subtotal * ((float) $this->discount_value / 100), 2)
            : min((float) $this->discount_value, $subtotal);

        $discountedSubtotal = $subtotal - $discount;

        // Auto fallback to room type tax percent if tax_rate is missing
        $tax_rate = (float) ($this->tax_rate ?? ($this->rooms->first()?->roomType?->tax_percent ?? 15));
        $tax = round($discountedSubtotal * ($tax_rate / 100), 2);
        $total = round($discountedSubtotal + $tax, 2);

        return compact('subtotal', 'discount', 'tax', 'total', 'tax_rate');
    }

    public function getEstimatedTotalAttribute(): float
    {
        return $this->calculateCharges()['total'];
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments->sum('amount');
    }

    public function getBalanceDueAttribute(): float
    {
        return round($this->estimated_total - $this->total_paid, 2);
    }
}
