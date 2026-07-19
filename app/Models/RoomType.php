<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class RoomType extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = ['name', 'hotel_id'];

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
}
