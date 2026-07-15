<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Request-level cache to avoid duplicate queries.
     */
    protected static array $cache = [];

    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (!array_key_exists($key, static::$cache)) {
            $setting = static::where('key', $key)->first();
            static::$cache[$key] = $setting ? $setting->value : $default;
        }
        return static::$cache[$key];
    }

    /**
     * Set (upsert) a setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        static::$cache[$key] = $value;
    }

    /**
     * Get all settings as a key => value array.
     */
    public static function all_map(): array
    {
        $settings = static::all()->pluck('value', 'key')->toArray();
        static::$cache = array_merge(static::$cache, $settings);
        return $settings;
    }
}
