<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class CheckIn extends Model
{
    use BelongsToTenant;

    protected $table = 'checkins';
    protected $fillable = ['reservation_id', 'booking_code', 'checkin_datetime', 'user_id', 'remarks', 'hotel_id'];
    
    protected $casts = [
        'checkin_datetime' => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
