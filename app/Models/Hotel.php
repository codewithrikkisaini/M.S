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
        'hotel_code',
        'email',
        'phone',
        'address',
        'status', // pending, approved, rejected
        'account_status', // pending_approval, active, suspended, cancelled
        'registration_notes',
        'approved_at',
        'approved_by',
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
            if (empty($hotel->hotel_code)) {
                $hotel->hotel_code = self::generateNextHotelCode();
            }
            if (empty($hotel->account_status)) {
                $hotel->account_status = 'pending_approval';
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

    public static function generateNextHotelCode(): string
    {
        $maxId = (int) self::max('id') + 1;
        return 'LDG-' . str_pad((string)$maxId, 6, '0', STR_PAD_LEFT);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)
            ->orWhere('slug', $value)
            ->orWhere('hotel_code', $value)
            ->firstOrFail();
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

    public function documents()
    {
        return $this->hasMany(HotelDocument::class);
    }
}
