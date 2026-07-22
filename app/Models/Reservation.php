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
        'discount_type', 'discount_value', 'tax_rate', 'misc_charge', 'pricing_mode',
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

    public function calculateCharges(?int $nights = null, string $pricingMode = 'auto'): array
    {
        $nights = $nights ?? $this->nights;

        $subtotal = $this->calculateRoomCharges($nights, $pricingMode);

        $discount = $this->discount_type === 'Percentage'
            ? round($subtotal * ((float) $this->discount_value / 100), 2)
            : min((float) $this->discount_value, $subtotal);

        $discountedSubtotal = $subtotal - $discount;
        $tax_rate = (float) ($this->tax_rate ?? 15);
        $tax = round($discountedSubtotal * ($tax_rate / 100), 2);
        $misc = (float) ($this->misc_charge ?? 0);
        $total = round($discountedSubtotal + $tax + $misc, 2);

        return compact('subtotal', 'discount', 'tax', 'total', 'tax_rate', 'misc');
    }

    protected function calculateRoomCharges(int $nights, string $pricingMode = 'auto'): float
    {
        if ($this->rooms->isEmpty()) {
            return 0.0;
        }

        return (float) $this->rooms->sum(function ($room) use ($nights, $pricingMode) {
            if ($room->roomType) {
                return $room->roomType->calculateChargeForNights($nights, $pricingMode);
            }

            return $nights * ((float) ($room->pivot?->price ?? $room->price ?? 0));
        });
    }

    public function getEstimatedTotalAttribute(): float
    {
        return $this->calculateCharges(null, $this->pricing_mode ?? 'auto')['total'];
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
