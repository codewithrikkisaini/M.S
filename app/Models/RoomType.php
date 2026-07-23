<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class RoomType extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'name',
        'hotel_id',
        'daily_rate',
        'weekly_rate',
        'monthly_rate',
        'tax_percent',
        'status',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function getBasePriceAttribute()
    {
        return (float) ($this->daily_rate ?: 59.95);
    }

    public function getBaseOccupancyAttribute()
    {
        return 2;
    }
}
