<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    protected $fillable = [
        'device_id',
        'username',
        'platform',
        'device_name',
        'os_version',
        'app_version',
        'ip_address',
        'country',
        'country_code',
        'city',
        'last_active_at',
        'total_watch_seconds',
        'sessions_count',
    ];

    protected function casts(): array
    {
        return [
            'last_active_at' => 'datetime',
            'total_watch_seconds' => 'integer',
            'sessions_count' => 'integer',
        ];
    }

    public function isOnline(): bool
    {
        return $this->last_active_at && $this->last_active_at->diffInMinutes(now()) <= 15;
    }

    public function getDisplayNameAttribute(): string
    {
        if (!empty($this->username)) {
            return $this->username;
        }
        $hash = strtoupper(substr(md5($this->device_id), 0, 4));
        return "MovieCat #{$hash}";
    }

    public function getCountryFlagAttribute(): string
    {
        $code = strtoupper(trim($this->country_code ?: ''));
        if (strlen($code) === 2 && ctype_alpha($code)) {
            return mb_chr(127397 + ord($code[0]), 'UTF-8') . mb_chr(127397 + ord($code[1]), 'UTF-8');
        }
        return '🌐';
    }

    public function getCountryDisplayAttribute(): string
    {
        $name = $this->country ?: ($this->country_code ?: 'Unknown');
        return "{$this->country_flag} {$name}";
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'device_id', 'device_id');
    }

    public function watchProgress(): HasMany
    {
        return $this->hasMany(WatchProgress::class, 'device_id', 'device_id')
            ->orderByDesc('last_watched_at');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class, 'device_id', 'device_id')
            ->orderByDesc('created_at');
    }

    public function adEvents(): HasMany
    {
        return $this->hasMany(AdEvent::class, 'device_id', 'device_id')
            ->orderByDesc('created_at');
    }

    public function errors(): HasMany
    {
        return $this->hasMany(AppError::class, 'device_id', 'device_id')
            ->orderByDesc('created_at');
    }



    public function downloads(): HasMany
    {
        return $this->hasMany(DownloadEvent::class, 'device_id', 'device_id');
    }

    public function getFormattedWatchTimeAttribute(): string
    {
        $seconds = (int)$this->total_watch_seconds;
        if ($seconds <= 0) return '0m';
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds % 3600) / 60);
        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }
        return "{$mins}m";
    }

}
