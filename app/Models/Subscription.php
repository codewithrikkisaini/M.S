<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Subscription extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'hotel_id',
        'subscription_plan_id',
        'status',
        'amount',
        'currency',
        'billing_cycle',
        'starts_at',
        'ends_at',
        'trial_starts_at',
        'trial_ends_at',
        'renews_at',
        'cancelled_at',
        'last_reminder_sent_at',
        'reminder_logs',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_starts_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'renews_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'last_reminder_sent_at' => 'datetime',
        'reminder_logs' => 'array',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function invoices()
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }
}
