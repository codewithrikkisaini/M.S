<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Invoice extends Model
{
    use BelongsToTenant;

    protected $fillable = ['invoice_number', 'checkout_id', 'hotel_id', 'total', 'total_amount', 'status'];

    public function checkout()
    {
        return $this->belongsTo(CheckOut::class);
    }
}
