<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'media_id',
        'media_type',
        'title',
        'poster',
        'season',
        'episode',
        'file_size_bytes',
        'quality',
        'status',
    ];

    protected $casts = [
        'season' => 'integer',
        'episode' => 'integer',
        'file_size_bytes' => 'integer',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id', 'device_id');
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int)$this->file_size_bytes;
        if ($bytes <= 0) return 'Unknown';
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        return number_format($bytes / 1024, 0) . ' KB';
    }
}
