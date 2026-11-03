<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    protected $fillable = [
        'media_item_id',
        'season_id',
        'season_number',
        'episode_number',
        'title',
        'overview',
        'still_path',
        'duration',
        'stream_url',
        'direct_url',
        'quality_options',
    ];

    protected function casts(): array
    {
        return [
            'season_number' => 'integer',
            'episode_number' => 'integer',
            'duration' => 'integer',
            'quality_options' => 'array',
        ];
    }

    public function mediaItem(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
}