<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdEvent;
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
}