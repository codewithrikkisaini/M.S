<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Support\Facades\Auth;

class Setting extends Model
{
    use BelongsToTenant;

    protected $fillable = ['key', 'value', 'hotel_id'];

    /**
     * Request-level cache to avoid duplicate queries.
     */
    protected static array $cache = [];

    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $hotelId = Auth::check() ? Auth::user()->hotel_id : null;
        $cacheKey = ($hotelId ?: 'global') . '_' . $key;

        if (!array_key_exists($cacheKey, static::$cache)) {
            $setting = static::where('key', $key)->first();
            static::$cache[$cacheKey] = $setting ? $setting->value : $default;
        }
        return static::$cache[$cacheKey];
    }

    /**
     * Set (upsert) a setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        $hotelId = Auth::check() ? Auth::user()->hotel_id : null;
        $cacheKey = ($hotelId ?: 'global') . '_' . $key;

        static::updateOrCreate(
            ['key' => $key, 'hotel_id' => $hotelId],
            ['value' => $value]
        );
        static::$cache[$cacheKey] = $value;
    }

    /**
     * Get all settings as a key => value array.
     */
    public static function all_map(): array
    {
        $settings = static::all()->pluck('value', 'key')->toArray();
        $hotelId = Auth::check() ? Auth::user()->hotel_id : null;
        $prefix = ($hotelId ?: 'global') . '_';
        
        foreach ($settings as $key => $val) {
            static::$cache[$prefix . $key] = $val;
        }
        return $settings;
    }
}
