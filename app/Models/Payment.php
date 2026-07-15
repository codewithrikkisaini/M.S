<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['reservation_id', 'amount', 'payment_type', 'paid_at'];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount'  => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
