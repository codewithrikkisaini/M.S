<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Guest extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'guest_id', 'name', 'email', 'phone', 
        'nationality', 'passport_number', 'address', 'hotel_id'
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
