<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaItem extends Model
{
    protected $fillable = [
        'tmdb_id',
        'moviebox_id',
        'title',
        'original_title',
        'overview',
        'media_type',
        'poster',
        'backdrop',
        'year',
        'runtime',
        'vote_average',
        'genres',
        'is_featured',
        'is_trending',
        'status',
        'direct_url',
        'quality_options',
    ];

    protected function casts(): array
    {
        return [
            'genres' => 'array',
            'quality_options' => 'array',
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'vote_average' => 'float',
            'tmdb_id' => 'integer',
        ];
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class)->orderBy('season_number');
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }
}