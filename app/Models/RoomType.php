<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class RoomType extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = ['name', 'hotel_id', 'daily_rate', 'weekly_rate', 'monthly_rate', 'tax_percentage', 'status'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function getBasePriceAttribute()
    {
        return $this->rooms()->avg('price') ?: 150.00;
    }

    public function getBaseOccupancyAttribute()
    {
        return 2;
    }

    public function calculateChargeForNights(int $nights, string $mode = 'auto'): float
    {
        $daily = (float) $this->daily_rate;
        $weekly = (float) $this->weekly_rate;
        $monthly = (float) $this->monthly_rate;

        $nights = max(0, $nights);
        if ($nights === 0) {
            return 0.0;
        }

        if ($mode === 'daily') {
            return round($daily * $nights, 2);
        }

        if ($mode === 'weekly') {
            return round($weekly * max(1, intdiv($nights + 6, 7)), 2);
        }

        if ($mode === 'monthly') {
            return round($monthly * max(1, intdiv($nights + 29, 30)), 2);
        }

        $total = 0.0;
        $remaining = $nights;

        if ($remaining >= 30 && $monthly > 0) {
            $months = intdiv($remaining, 30);
            $total += $months * $monthly;
            $remaining %= 30;
        }

        if ($remaining >= 7 && $weekly > 0) {
            $weeks = intdiv($remaining, 7);
            $total += $weeks * $weekly;
            $remaining %= 7;
        }

        $total += $remaining * $daily;

        return round($total, 2);
    }

    public function getDailyWithTaxAttribute(): float
    {
        return round((float) $this->daily_rate * (1 + ((float) $this->tax_percentage / 100)), 2);
    }

    public function getWeeklyWithTaxAttribute(): float
    {
        return round((float) $this->weekly_rate * (1 + ((float) $this->tax_percentage / 100)), 2);
    }

    public function getMonthlyWithTaxAttribute(): float
    {
        return round((float) $this->monthly_rate * (1 + ((float) $this->tax_percentage / 100)), 2);
    }
}
