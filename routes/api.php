<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\TelemetryController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => 'MovieCat API Server',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Mobile Client API v1 with Global Rate Limiting (60 requests/min per IP)
Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    
    // Remote Configuration & Live AdMob Switch
    Route::get('/config', [ConfigController::class, 'index']);

    // Device Heartbeat & Registration (Active Devices Tracking)
    Route::post('/device/heartbeat', [DeviceController::class, 'heartbeat']);

    // Mobile Guest Token & Authentication (Strict Rate Limiting against Brute-Force)
    Route::post('/auth/guest-token', [AuthController::class, 'guestToken'])->middleware('throttle:20,1');
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/user', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
    });

    // Media Catalog & Streams
    Route::get('/featured', [MediaController::class, 'featured']);
    Route::get('/trending', [MediaController::class, 'trending']);
    Route::get('/trending/country', [MediaController::class, 'trendingByCountry']);
    Route::get('/movies', [MediaController::class, 'movies']);
    Route::get('/series', [MediaController::class, 'series']);
    Route::get('/media/{id}', [MediaController::class, 'show']);
    Route::get('/series/{id}/seasons/{season}/episodes', [MediaController::class, 'seasonEpisodes']);
    Route::get('/media/{id}/similar', [MediaController::class, 'similar']);

    // Watch Progress & History Cloud Sync
    Route::get('/sync/progress', [SyncController::class, 'getProgress']);
    Route::post('/sync/progress', [SyncController::class, 'saveProgress']);
    Route::delete('/sync/progress/{mediaId}', [SyncController::class, 'deleteProgress']);

    // Bookmarks / Watchlist Cloud Sync
    Route::get('/sync/bookmarks', [SyncController::class, 'getBookmarks']);
    Route::post('/sync/bookmarks', [SyncController::class, 'toggleBookmark']);

    // Ad & Telemetry Analytics (Throttled to prevent flooding)
    Route::post('/telemetry/ad-event', [TelemetryController::class, 'recordAdEvent'])->middleware('throttle:120,1');
    Route::post('/telemetry/error', [TelemetryController::class, 'recordAppError'])->middleware('throttle:60,1');
        // Search Query & Linked Media Telemetry
    Route::post('/telemetry/search', [TelemetryController::class, 'recordSearch'])->middleware('throttle:60,1');
    Route::post('/telemetry/search/link', [TelemetryController::class, 'linkSearchMedia'])->middleware('throttle:60,1');
    Route::post('/telemetry/download', [TelemetryController::class, 'recordDownload'])->middleware('throttle:60,1');
    Route::post('/telemetry/downloads/sync', [TelemetryController::class, 'syncVaultDownloads'])->middleware('throttle:60,1');

    // Vault Subscription & East Africa Payment Gateways (M-Pesa, PayHero, Flutterwave)
    Route::get('/subscription/status', [SubscriptionController::class, 'status']);
    Route::post('/subscription/pay', [SubscriptionController::class, 'pay'])->middleware('throttle:20,1');
    Route::get('/subscription/query', [SubscriptionController::class, 'query'])->middleware('throttle:60,1');
    Route::post('/subscription/webhook/{gateway}', [SubscriptionController::class, 'webhook']);
    Route::post('/webhooks/mpesa', fn(\Illuminate\Http\Request $r) => app(SubscriptionController::class)->webhook($r, 'mpesa'));
});
