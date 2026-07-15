<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckOut extends Model
{
    protected $table = 'checkouts';
    protected $fillable = ['reservation_id', 'checkout_datetime', 'nights', 'subtotal', 'discount', 'tax', 'tax_rate', 'total_amount'];
    
    protected $casts = [
        'checkout_datetime' => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'checkout_id');
    }
}
