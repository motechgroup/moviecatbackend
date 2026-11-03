<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdEvent extends Model
{
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id', 'device_id');
    }

    public $timestamps = false;

    protected $fillable = [
        'device_id',
        'ad_unit_id',
        'ad_type',
        'event_type',
        'extra',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'extra' => 'array',
            'created_at' => 'datetime',
        ];
    }
}