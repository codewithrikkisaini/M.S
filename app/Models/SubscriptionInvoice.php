<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class SubscriptionInvoice extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'hotel_id',
        'subscription_plan_id',
        'invoice_number',
        'amount',
        'status',
        'billing_date',
        'due_date',
        'paid_at',
        'payment_method',
    ];

    protected $casts = [
        'billing_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
