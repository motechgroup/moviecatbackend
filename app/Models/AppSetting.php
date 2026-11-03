<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        } catch (Throwable) {
            return $default;
        }
    }

    public static function set(string $key, mixed $value, string $group = 'general', ?string $desc = null): ?static
    {
        try {
            return static::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => $group,
                    'description' => $desc,
                ]
            );
        } catch (Throwable) {
            return null;
        }
    }
}