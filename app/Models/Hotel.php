<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status', // pending, approved, rejected
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->whereIn('status', ['active', 'trialing'])->latestOfMany();
    }

    public function subscriptionInvoices()
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }
}
