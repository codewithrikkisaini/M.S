<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'status', // pending, approved, rejected
        'business_name',
        'owner_name',
        'tax_id',
        'company_reg_number',
        'business_license_number',
        'whatsapp',
        'website',
        'country',
        'state',
        'city',
        'postal_code',
        'timezone',
        'currency',
        'rooms_count',
        'category',
        'property_type',
        'current_pms',
        'current_channel_manager',
        'current_website',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function images()
    {
        return $this->hasMany(HotelImage::class);
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
