<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
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

    protected static function booted(): void
    {
        static::creating(function (Hotel $hotel) {
            if (empty($hotel->slug) && !empty($hotel->name)) {
                $hotel->slug = Str::slug($hotel->name);
            }
        });

        static::updating(function (Hotel $hotel) {
            if ($hotel->isDirty('name') || empty($hotel->slug)) {
                if (!empty($hotel->name)) {
                    $hotel->slug = Str::slug($hotel->name);
                }
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getSlugAttribute($value): string
    {
        return $value ?: Str::slug($this->name ?? 'hotel');
    }

    public function getUrlAttribute(): string
    {
        $baseSlug = $this->slug ?: Str::slug($this->name ?? 'hotel');
        if (Str::endsWith($baseSlug, '-' . $this->id)) {
            return url('/hotel/' . $baseSlug);
        }
        return url('/hotel/' . $baseSlug . '-' . $this->id);
    }

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

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
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
