<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchProgress extends Model
{
    protected $table = 'watch_progress';

    protected $fillable = [
        'user_id',
        'device_id',
        'media_id',
        'media_type',
        'title',
        'poster',
        'season',
        'episode',
        'current_time',
        'duration',
        'completed',
        'last_watched_at',
    ];

    protected function casts(): array
    {
        return [
            'season' => 'integer',
            'episode' => 'integer',
            'current_time' => 'float',
            'duration' => 'float',
            'completed' => 'boolean',
            'last_watched_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id', 'device_id');
    }
}