<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['invoice_number', 'checkout_id'];

    public function checkout()
    {
        return $this->belongsTo(CheckOut::class);
    }
}
