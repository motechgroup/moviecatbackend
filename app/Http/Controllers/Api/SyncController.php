<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Device;
use App\Models\WatchProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class SyncController extends Controller
{
    public function getProgress(Request $request): JsonResponse
    {
        $deviceId = $request->header('X-Device-Id', $request->query('device_id'));
        $userId = $request->user()?->id;

        try {
            $query = WatchProgress::query();
            if ($userId) {
                $query->where('user_id', $userId);
            } elseif ($deviceId) {
                $query->where('device_id', $deviceId);
            } else {
                return response()->json(['success' => true, 'progress' => (object)[]]);
            }

            $items = $query->orderByDesc('last_watched_at')->get();

            $map = [];
            foreach ($items as $item) {
                $key = $item->media_type === 'tv' && $item->season && $item->episode
                    ? "tv_{$item->media_id}_s{$item->season}_e{$item->episode}"
                    : "{$item->media_type}_{$item->media_id}";

                $map[$key] = [
                    'id' => $item->media_id,
                    'mediaType' => $item->media_type,
                    'title' => $item->title,
                    'poster' => $item->poster,
                    'season' => $item->season,
                    'episode' => $item->episode,
                    'currentTime' => (float)$item->current_time,
                    'duration' => (float)$item->duration,
                    'completed' => (bool)$item->completed,
                    'lastWatched' => $item->last_watched_at?->toIso8601String(),
                ];
            }

            return response()->json([
                'success' => true,
                'progress' => $map,
            ]);
        } catch (Throwable) {
            return response()->json([
                'success' => true,
                'progress' => (object)[],
            ]);
        }
    }

    public function saveProgress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'media_id' => 'required|string',
            'media_type' => 'required|string|in:movie,tv',
            'title' => 'required|string',
            'poster' => 'nullable|string',
            'season' => 'nullable|integer',
            'episode' => 'nullable|integer',
            'current_time' => 'required|numeric',
            'duration' => 'required|numeric',
            'completed' => 'nullable|boolean',
            'device_id' => 'nullable|string',
        ]);

        $deviceId = $validated['device_id'] ?: $request->header('X-Device-Id', 'device_default');
        $userId = $request->user()?->id;

        try {
            $matchCriteria = [
                'device_id' => $deviceId,
                'media_id' => $validated['media_id'],
                'media_type' => $validated['media_type'],
                'season' => $validated['season'] ?? null,
                'episode' => $validated['episode'] ?? null,
            ];

            $cfCountry = strtoupper(trim((string)$request->header('CF-IPCountry')));
            $payloadCountry = strtoupper(trim((string)($request->input('country_code') ?: $request->header('X-Country-Code'))));
            $countryCode = (strlen($cfCountry) === 2 && $cfCountry !== 'XX') ? $cfCountry : (strlen($payloadCountry) === 2 ? $payloadCountry : null);
            if (!$countryCode) {
                $countryCode = Device::where('device_id', $deviceId)->value('country_code') ?: 'KE';
            }

            $progress = WatchProgress::updateOrCreate($matchCriteria, [
                'user_id' => $userId,
                'title' => $validated['title'],
                'poster' => $validated['poster'] ?? null,
                'country_code' => $countryCode,
                'current_time' => $validated['current_time'],
                'duration' => $validated['duration'],
                'completed' => $validated['completed'] ?? false,
                'last_watched_at' => now(),
            ]);

            // Ensure device exists and update watch time
            $device = Device::firstOrCreate(
                ['device_id' => $deviceId],
                [
                    'username' => 'MovieCat #' . strtoupper(substr(md5($deviceId), 0, 4)),
                    'platform' => $request->header('X-Platform', 'android'),
                    'device_name' => 'Mobile Device',
                    'ip_address' => $request->ip(),
                    'country' => 'Kenya',
                    'country_code' => 'KE',
                    'last_active_at' => now(),
                    'sessions_count' => 1,
                ]
            );
            $device->increment('total_watch_seconds', min(15, (int)$validated['current_time']));
            $device->update(['last_active_at' => now()]);

            return response()->json([
                'success' => true,
                'data' => $progress,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Progress saved locally (database offline)',
            ]);
        }
    }

    public function deleteProgress(Request $request, string $mediaId): JsonResponse
    {
        $deviceId = $request->header('X-Device-Id', $request->query('device_id'));
        $userId = $request->user()?->id;

        try {
            $query = WatchProgress::where('media_id', $mediaId);
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('device_id', $deviceId);
            }
            $query->delete();
        } catch (Throwable) {}

        return response()->json(['success' => true, 'message' => 'Progress cleared']);
    }

    public function getBookmarks(Request $request): JsonResponse
    {
        $deviceId = $request->header('X-Device-Id', $request->query('device_id'));
        $userId = $request->user()?->id;

        try {
            $query = Bookmark::query();
            if ($userId) {
                $query->where('user_id', $userId);
            } elseif ($deviceId) {
                $query->where('device_id', $deviceId);
            } else {
                return response()->json(['success' => true, 'bookmarks' => []]);
            }

            $bookmarks = $query->orderByDesc('created_at')->get();

            return response()->json([
                'success' => true,
                'bookmarks' => $bookmarks,
            ]);
        } catch (Throwable) {
            return response()->json(['success' => true, 'bookmarks' => []]);
        }
    }

    public function toggleBookmark(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'media_id' => 'required|string',
            'media_type' => 'required|string|in:movie,tv',
            'title' => 'required|string',
            'poster' => 'nullable|string',
            'backdrop' => 'nullable|string',
            'year' => 'nullable|string',
            'vote_average' => 'nullable|numeric',
            'device_id' => 'nullable|string',
        ]);

        $deviceId = $validated['device_id'] ?: $request->header('X-Device-Id', 'device_default');
        $userId = $request->user()?->id;

        try {
            $existing = Bookmark::where('device_id', $deviceId)
                ->where('media_id', $validated['media_id'])
                ->first();

            if ($existing) {
                $existing->delete();
                return response()->json([
                    'success' => true,
                    'bookmarked' => false,
                    'message' => 'Removed from watchlist',
                ]);
            }

            $bookmark = Bookmark::create([
                'user_id' => $userId,
                'device_id' => $deviceId,
                'media_id' => $validated['media_id'],
                'media_type' => $validated['media_type'],
                'title' => $validated['title'],
                'poster' => $validated['poster'] ?? null,
                'backdrop' => $validated['backdrop'] ?? null,
                'year' => $validated['year'] ?? null,
                'vote_average' => $validated['vote_average'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'bookmarked' => true,
                'data' => $bookmark,
                'message' => 'Saved to watchlist',
            ]);
        } catch (Throwable) {
            return response()->json([
                'success' => true,
                'bookmarked' => true,
                'message' => 'Saved locally',
            ]);
        }
    }
}