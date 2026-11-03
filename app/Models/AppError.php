<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppError extends Model
{
    protected $fillable = [
        'device_id',
        'error_type',
        'error_message',
        'screen',
        'severity',
        'stack_trace',
        'metadata',
        'platform',
        'app_version',
        'resolved',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'resolved' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id', 'device_id');
    }

    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'fatal' => 'rose',
            'error' => 'amber',
            'warning' => 'yellow',
            default => 'slate',
        };
    }
}
