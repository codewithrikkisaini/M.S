<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Payment extends Model
{
    use BelongsToTenant;

    protected $fillable = ['reservation_id', 'amount', 'payment_type', 'paid_at', 'hotel_id'];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount'  => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
