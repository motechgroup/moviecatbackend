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
        'module',
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

    /**
     * Get definitions and styling for all application modules
     */
    public static function getModulesDefinition(): array
    {
        return [
            'player' => [
                'key' => 'player',
                'name' => 'Video Player & Streaming',
                'short_name' => 'Player & Streaming',
                'icon' => '🎬',
                'description' => 'Playback engine, ExoPlayer, HLS/MP4 streaming, buffering & CDN token errors',
                'badge_bg' => 'bg-rose-500/10',
                'badge_text' => 'text-rose-400',
                'badge_border' => 'border-rose-500/20',
                'accent_hex' => '#F43F5E',
            ],
            'vault' => [
                'key' => 'vault',
                'name' => 'Offline Vault & Downloads',
                'short_name' => 'Offline Vault',
                'icon' => '📦',
                'description' => 'File system storage, background download tasks, disk capacity & offline cache',
                'badge_bg' => 'bg-emerald-500/10',
                'badge_text' => 'text-emerald-400',
                'badge_border' => 'border-emerald-500/20',
                'accent_hex' => '#10B981',
            ],
            'search' => [
                'key' => 'search',
                'name' => 'Search & Discovery',
                'short_name' => 'Search',
                'icon' => '🔍',
                'description' => 'Instant title discovery, filter chips, search queries & auto-complete',
                'badge_bg' => 'bg-sky-500/10',
                'badge_text' => 'text-sky-400',
                'badge_border' => 'border-sky-500/20',
                'accent_hex' => '#0EA5E9',
            ],
            'catalogue' => [
                'key' => 'catalogue',
                'name' => 'Catalogue & Media Details',
                'short_name' => 'Catalogue & Details',
                'icon' => '🎞️',
                'description' => 'Home carousels, movies/series listings, media details & episode selector',
                'badge_bg' => 'bg-purple-500/10',
                'badge_text' => 'text-purple-400',
                'badge_border' => 'border-purple-500/20',
                'accent_hex' => '#A855F7',
            ],
            'vip' => [
                'key' => 'vip',
                'name' => 'VIP, Paywall & M-Pesa',
                'short_name' => 'VIP & Paywall',
                'icon' => '💎',
                'description' => 'Subscription licensing, M-Pesa STK push, payment verification & validity checks',
                'badge_bg' => 'bg-amber-500/10',
                'badge_text' => 'text-amber-400',
                'badge_border' => 'border-amber-500/20',
                'accent_hex' => '#F59E0B',
            ],
            'history' => [
                'key' => 'history',
                'name' => 'Watch History & Cloud Sync',
                'short_name' => 'History & Sync',
                'icon' => '⏱️',
                'description' => 'Playback resume points, watch timestamps, bookmarks & multi-device sync',
                'badge_bg' => 'bg-indigo-500/10',
                'badge_text' => 'text-indigo-400',
                'badge_border' => 'border-indigo-500/20',
                'accent_hex' => '#6366F1',
            ],
            'ads' => [
                'key' => 'ads',
                'name' => 'Ads & Monetization',
                'short_name' => 'Ads & Monetization',
                'icon' => '📢',
                'description' => 'Google AdMob SDK, rewarded interstitial ads & custom house banner ads',
                'badge_bg' => 'bg-yellow-500/10',
                'badge_text' => 'text-yellow-400',
                'badge_border' => 'border-yellow-500/20',
                'accent_hex' => '#EAB308',
            ],
            'core' => [
                'key' => 'core',
                'name' => 'Core Engine & Network',
                'short_name' => 'Core & Network',
                'icon' => '⚙️',
                'description' => 'React Native runtime, unhandled fatal exceptions, HTTP API client & device lifecycle',
                'badge_bg' => 'bg-slate-500/10',
                'badge_text' => 'text-slate-400',
                'badge_border' => 'border-slate-500/20',
                'accent_hex' => '#64748B',
            ],
        ];
    }

    /**
     * Automatically determine the application module for any error
     */
    public static function determineModule(?string $screen, ?string $errorType = null, ?string $errorMessage = null, $metadata = null): string
    {
        $screenLower = strtolower(trim((string)$screen));
        $typeLower = strtolower(trim((string)$errorType));
        $msgLower = strtolower(trim((string)$errorMessage));

        $metaStr = '';
        if (is_array($metadata)) {
            $metaStr = strtolower(json_encode($metadata));
        } elseif (is_string($metadata)) {
            $metaStr = strtolower($metadata);
        }

        $allText = $screenLower . ' ' . $typeLower . ' ' . $msgLower . ' ' . $metaStr;

        // 1. Video Player & Streaming
        if (
            str_contains($screenLower, 'player') ||
            str_contains($typeLower, 'playback') ||
            str_contains($allText, 'exoplayer') ||
            str_contains($allText, 'mediacodec') ||
            str_contains($allText, 'videoplayer') ||
            str_contains($allText, 'hls') ||
            str_contains($allText, 'buffering') ||
            str_contains($allText, 'audio track') ||
            str_contains($allText, 'response code: 403') ||
            str_contains($allText, 'response code: 404') ||
            str_contains($allText, 'source error') ||
            str_contains($allText, 'bitrate')
        ) {
            return 'player';
        }

        // 2. Offline Vault & Downloads
        if (
            str_contains($screenLower, 'download') ||
            str_contains($screenLower, 'vault') ||
            str_contains($typeLower, 'download') ||
            str_contains($allText, 'downloadasync') ||
            str_contains($allText, 'filesystem') ||
            str_contains($allText, 'enospc') ||
            str_contains($allText, 'disk full') ||
            str_contains($allText, 'offline media') ||
            str_contains($allText, 'writefile')
        ) {
            return 'vault';
        }

        // 3. Search & Discovery
        if (
            str_contains($screenLower, 'search') ||
            str_contains($screenLower, 'explore') ||
            str_contains($typeLower, 'search') ||
            str_contains($allText, 'searchquery') ||
            str_contains($allText, 'autocomplete')
        ) {
            return 'search';
        }

        // 4. VIP, Paywall & M-Pesa
        if (
            str_contains($screenLower, 'subscription') ||
            str_contains($screenLower, 'vip') ||
            str_contains($screenLower, 'paywall') ||
            str_contains($screenLower, 'billing') ||
            str_contains($typeLower, 'payment') ||
            str_contains($typeLower, 'subscription') ||
            str_contains($allText, 'mpesa') ||
            str_contains($allText, 'daraja') ||
            str_contains($allText, 'stk') ||
            str_contains($allText, 'checkout') ||
            str_contains($allText, 'license')
        ) {
            return 'vip';
        }

        // 5. Watch History & Sync
        if (
            str_contains($screenLower, 'history') ||
            str_contains($screenLower, 'sync') ||
            str_contains($typeLower, 'sync') ||
            str_contains($typeLower, 'bookmark') ||
            str_contains($allText, 'watch progress') ||
            str_contains($allText, 'bookmark') ||
            str_contains($allText, 'resume progress')
        ) {
            return 'history';
        }

        // 6. Ads & Monetization
        if (
            str_contains($screenLower, 'admob') ||
            str_contains($screenLower, 'ad') ||
            str_contains($typeLower, 'ad') ||
            str_contains($allText, 'admob') ||
            str_contains($allText, 'interstitial') ||
            str_contains($allText, 'rewarded') ||
            str_contains($allText, 'banner ad') ||
            str_contains($allText, 'ad failed')
        ) {
            return 'ads';
        }

        // 7. Catalogue & Media Details
        if (
            str_contains($screenLower, 'detail') ||
            str_contains($screenLower, 'home') ||
            str_contains($screenLower, 'movie') ||
            str_contains($screenLower, 'tv') ||
            str_contains($screenLower, 'series') ||
            str_contains($typeLower, 'catalogue') ||
            str_contains($allText, 'mediaitem') ||
            str_contains($allText, 'season picker') ||
            str_contains($allText, 'episode')
        ) {
            return 'catalogue';
        }

        // 8. Core Engine & Network (Default)
        return 'core';
    }

    public function getModuleAttribute($value): string
    {
        if (!empty($value)) {
            return $value;
        }
        return self::determineModule($this->screen, $this->error_type, $this->error_message, $this->metadata);
    }

    public function getModuleMetaAttribute(): array
    {
        $mods = self::getModulesDefinition();
        $key = $this->module;
        return $mods[$key] ?? $mods['core'];
    }
}
