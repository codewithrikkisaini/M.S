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
        'subscription_id',
        'subscription_plan_id',
        'invoice_number',
        'amount',
        'currency',
        'notes',
        'status',
        'payment_status',
        'billing_date',
        'due_date',
        'paid_at',
        'payment_method',
        'paypal_order_id',
        'paypal_transaction_id',
        'paypal_payer_email',
    ];

    protected $casts = [
        'billing_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public static function generateNextInvoiceNumber(): string
    {
        $year = date('Y');
        $maxId = (int) self::max('id') + 1;
        return 'LDG-' . $year . '-' . str_pad((string)$maxId, 6, '0', STR_PAD_LEFT);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
