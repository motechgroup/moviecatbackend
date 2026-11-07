<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdEvent;
use App\Models\AppError;
use App\Models\Device;
use App\Models\DownloadEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelemetryController extends Controller
{
    public function recordAdEvent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ad_type' => 'required|string|in:interstitial,rewarded,banner',
            'event_type' => 'required|string|in:impression,click,failed,dismissed',
            'ad_unit_id' => 'nullable|string',
            'device_id' => 'nullable|string',
            'extra' => 'nullable|array',
        ]);

        $deviceId = $validated['device_id'] ?? $request->header('X-Device-Id');
        if ($deviceId) {
            try {
                $device = Device::where('device_id', $deviceId)->first();
                if ($device) {
                    $device->update(['last_active_at' => now()]);
                }
            } catch (\Throwable) {}
        }

        $event = AdEvent::create([
            'device_id' => $validated['device_id'] ?? $request->header('X-Device-Id'),
            'ad_unit_id' => $validated['ad_unit_id'] ?? null,
            'ad_type' => $validated['ad_type'],
            'event_type' => $validated['event_type'],
            'extra' => $validated['extra'] ?? null,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'eventId' => $event->id,
        ]);
    }

    public function recordAppError(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'error_message' => 'required|string',
            'error_type' => 'nullable|string|max:64',
            'screen' => 'nullable|string|max:64',
            'severity' => 'nullable|string|in:fatal,error,warning',
            'stack_trace' => 'nullable|string',
            'metadata' => 'nullable|array',
            'device_id' => 'nullable|string|max:128',
            'platform' => 'nullable|string|max:32',
            'app_version' => 'nullable|string|max:32',
        ]);

        $deviceId = $validated['device_id'] ?? $request->header('X-Device-Id');

        if ($deviceId) {
            try {
                $cfCountry = strtoupper(trim((string)$request->header('CF-IPCountry')));
                $countryCode = (strlen($cfCountry) === 2 && $cfCountry !== 'XX') ? $cfCountry : 'KE';
                $device = Device::where('device_id', $deviceId)->first();
                if (!$device) {
                    Device::create([
                        'device_id' => $deviceId,
                        'username' => 'MovieCat #' . strtoupper(substr(md5($deviceId), 0, 4)),
                        'platform' => $validated['platform'] ?? $request->header('X-Platform', 'android'),
                        'device_name' => 'Mobile Device',
                        'app_version' => $validated['app_version'] ?? '1.0.0',
                        'ip_address' => $request->ip(),
                        'country' => 'Kenya',
                        'country_code' => $countryCode,
                        'last_active_at' => now(),
                        'sessions_count' => 1,
                    ]);
                } else {
                    $device->update(['last_active_at' => now()]);
                }
            } catch (\Throwable) {}
        }

        $error = AppError::create([
            'device_id' => $deviceId,
            'error_type' => $validated['error_type'] ?? 'app_error',
            'error_message' => $validated['error_message'],
            'screen' => $validated['screen'] ?? null,
            'severity' => $validated['severity'] ?? 'error',
            'stack_trace' => $validated['stack_trace'] ?? null,
            'metadata' => $validated['metadata'] ?? null,
            'platform' => $validated['platform'] ?? $request->header('X-Platform'),
            'app_version' => $validated['app_version'] ?? '1.0.0',
            'resolved' => false,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'errorId' => $error->id,
        ], 201);
    }

        public function syncVaultDownloads(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => 'required|string|max:128',
            'downloads' => 'required|array',
            'downloads.*.media_id' => 'required|string|max:64',
            'downloads.*.media_type' => 'nullable|string|in:movie,tv',
            'downloads.*.title' => 'required|string|max:255',
            'downloads.*.poster' => 'nullable|string',
            'downloads.*.season' => 'nullable|integer',
            'downloads.*.episode' => 'nullable|integer',
            'downloads.*.file_size_bytes' => 'nullable|integer',
            'downloads.*.quality' => 'nullable|string|max:32',
            'downloads.*.status' => 'nullable|string|max:32',
        ]);

        $deviceId = $validated['device_id'];

        try {
            $device = Device::where('device_id', $deviceId)->first();
            if ($device) {
                $device->update(['last_active_at' => now()]);
            } else {
                $cfCountry = strtoupper(trim((string)$request->header('CF-IPCountry')));
                $countryCode = (strlen($cfCountry) === 2 && $cfCountry !== 'XX') ? $cfCountry : 'KE';
                Device::create([
                    'device_id' => $deviceId,
                    'username' => 'MovieCat #' . strtoupper(substr(md5($deviceId), 0, 4)),
                    'platform' => $request->header('X-Platform', 'android'),
                    'device_name' => 'Mobile Device',
                    'app_version' => '1.0.0',
                    'ip_address' => $request->ip(),
                    'country' => 'Kenya',
                    'country_code' => $countryCode,
                    'last_active_at' => now(),
                    'sessions_count' => 1,
                ]);
            }

            $syncedCount = 0;
            foreach ($validated['downloads'] as $item) {
                DownloadEvent::updateOrCreate(
                    [
                        'device_id' => $deviceId,
                        'media_id' => $item['media_id'],
                        'season' => $item['season'] ?? null,
                        'episode' => $item['episode'] ?? null,
                    ],
                    [
                        'media_type' => $item['media_type'] ?? 'movie',
                        'title' => $item['title'],
                        'poster' => $item['poster'] ?? null,
                        'file_size_bytes' => $item['file_size_bytes'] ?? 0,
                        'quality' => $item['quality'] ?? '1080p',
                        'status' => $item['status'] ?? 'completed',
                        'created_at' => now(),
                    ]
                );
                $syncedCount++;
            }

            return response()->json([
                'success' => true,
                'synced' => $syncedCount,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function recordDownload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => 'required|string|max:128',
            'media_id' => 'required|string|max:64',
            'media_type' => 'nullable|string|in:movie,tv',
            'title' => 'required|string|max:255',
            'poster' => 'nullable|string',
            'season' => 'nullable|integer',
            'episode' => 'nullable|integer',
            'file_size_bytes' => 'nullable|integer',
            'quality' => 'nullable|string|max:32',
            'status' => 'nullable|string|max:32',
        ]);

        $deviceId = $validated['device_id'];

        try {
            $device = Device::where('device_id', $deviceId)->first();
            if ($device) {
                $device->update(['last_active_at' => now()]);
            } else {
                $cfCountry = strtoupper(trim((string)$request->header('CF-IPCountry')));
                $countryCode = (strlen($cfCountry) === 2 && $cfCountry !== 'XX') ? $cfCountry : 'KE';
                Device::create([
                    'device_id' => $deviceId,
                    'username' => 'MovieCat #' . strtoupper(substr(md5($deviceId), 0, 4)),
                    'platform' => $request->header('X-Platform', 'android'),
                    'device_name' => 'Mobile Device',
                    'app_version' => '1.0.0',
                    'ip_address' => $request->ip(),
                    'country' => 'Kenya',
                    'country_code' => $countryCode,
                    'last_active_at' => now(),
                    'sessions_count' => 1,
                ]);
            }

            $event = DownloadEvent::updateOrCreate(
                [
                    'device_id' => $deviceId,
                    'media_id' => $validated['media_id'],
                    'season' => $validated['season'] ?? null,
                    'episode' => $validated['episode'] ?? null,
                ],
                [
                    'media_type' => $validated['media_type'] ?? 'movie',
                    'title' => $validated['title'],
                    'poster' => $validated['poster'] ?? null,
                    'file_size_bytes' => $validated['file_size_bytes'] ?? 0,
                    'quality' => $validated['quality'] ?? '1080p',
                    'status' => $validated['status'] ?? 'completed',
                    'created_at' => now(),
                ]
            );

            return response()->json(['success' => true, 'id' => $event->id]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * Record user search query
     */
    public function recordSearch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => 'nullable|string|max:128',
            'username' => 'nullable|string|max:64',
            'query' => 'required|string|max:255',
            'results_count' => 'nullable|integer',
            'media_id' => 'nullable|string|max:64',
            'media_type' => 'nullable|string|in:movie,tv',
            'title' => 'nullable|string|max:255',
            'poster' => 'nullable|string',
            'year' => 'nullable|string|max:16',
        ]);

        $searchLog = \App\Models\SearchLog::create([
            'device_id' => $validated['device_id'] ?? $request->header('X-Device-Id'),
            'username' => $validated['username'] ?? $request->header('X-Username'),
            'query' => trim($validated['query']),
            'results_count' => $validated['results_count'] ?? 0,
            'media_id' => $validated['media_id'] ?? null,
            'media_type' => $validated['media_type'] ?? null,
            'title' => $validated['title'] ?? null,
            'poster' => $validated['poster'] ?? null,
            'year' => $validated['year'] ?? null,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'search_id' => $searchLog->id,
        ], 201);
    }

    /**
     * Link clicked TV series or Movie to the user's search
     */
    public function linkSearchMedia(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => 'nullable|string|max:128',
            'username' => 'nullable|string|max:64',
            'query' => 'required|string|max:255',
            'media_id' => 'required|string|max:64',
            'media_type' => 'required|string|in:movie,tv',
            'title' => 'required|string|max:255',
            'poster' => 'nullable|string',
            'year' => 'nullable|string|max:16',
        ]);

        $deviceId = $validated['device_id'] ?? $request->header('X-Device-Id');
        $query = trim($validated['query']);

        // Find the recent search log for this query & device within last 15 minutes, or create fresh
        $recent = \App\Models\SearchLog::where('query', $query)
            ->where(function ($q) use ($deviceId) {
                if (!empty($deviceId)) {
                    $q->where('device_id', $deviceId);
                }
            })
            ->whereNull('title')
            ->latest()
            ->first();

        if ($recent) {
            $recent->update([
                'media_id' => $validated['media_id'],
                'media_type' => $validated['media_type'],
                'title' => $validated['title'],
                'poster' => $validated['poster'] ?? null,
                'year' => $validated['year'] ?? null,
            ]);
            $item = $recent;
        } else {
            $item = \App\Models\SearchLog::create([
                'device_id' => $deviceId,
                'username' => $validated['username'] ?? $request->header('X-Username'),
                'query' => $query,
                'results_count' => 1,
                'media_id' => $validated['media_id'],
                'media_type' => $validated['media_type'],
                'title' => $validated['title'],
                'poster' => $validated['poster'] ?? null,
                'year' => $validated['year'] ?? null,
                'ip_address' => $request->ip(),
            ]);
        }

        return response()->json([
            'success' => true,
            'search_id' => $item->id,
        ]);
    }

}
