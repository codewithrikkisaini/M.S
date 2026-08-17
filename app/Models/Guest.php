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
        'nationality', 'id_type', 'id_number', 'passport_number', 'address', 'hotel_id',
        'id_card_front', 'id_card_back', 'guest_photo'
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function blacklists()
    {
        return $this->hasMany(\App\Models\GuestBlacklist::class);
    }

    public function isBlacklisted(): bool
    {
        return $this->blacklists()->active()->exists();
    }
}
