<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdEvent;
use App\Models\AppError;
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
}
