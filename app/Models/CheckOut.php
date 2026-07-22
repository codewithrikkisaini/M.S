<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class CheckOut extends Model
{
    use BelongsToTenant;

    protected $table = 'checkouts';
    protected $fillable = ['reservation_id', 'checkout_datetime', 'nights', 'subtotal', 'discount', 'tax', 'tax_rate', 'misc_charge', 'total_amount', 'hotel_id'];
    
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
