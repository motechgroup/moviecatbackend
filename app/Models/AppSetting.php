<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Throwable;

class AppSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'json',
        ];
    }

    private static ?array $memoryCache = null;

    public static function getAllCached(): array
    {
        if (self::$memoryCache !== null) {
            return self::$memoryCache;
        }

        try {
            self::$memoryCache = Cache::remember('app_settings_all_dict', 600, function () {
                return static::all()->pluck('value', 'key')->toArray();
            });
        } catch (Throwable) {
            self::$memoryCache = [];
        }

        return self::$memoryCache ?: [];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $all = self::getAllCached();
            return array_key_exists($key, $all) ? $all[$key] : $default;
        } catch (Throwable) {
            return $default;
        }
    }

    public static function set(string $key, mixed $value, string $group = 'general', ?string $desc = null): ?static
    {
        try {
            self::$memoryCache = null;
            Cache::forget('app_settings_all_dict');

            $setting = static::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => $group,
                    'description' => $desc,
                ]
            );

            self::$memoryCache = null;
            Cache::forget('app_settings_all_dict');

            return $setting;
        } catch (Throwable) {
            return null;
        }
    }
}
