<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\TelemetryController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => 'MovieCat API Server',
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::prefix('v1')->group(function () {
    // Remote Configuration & Live AdMob Switch
    Route::get('/config', [ConfigController::class, 'index']);

    // Device Heartbeat & Registration (Active Devices Tracking)
    Route::post('/device/heartbeat', [DeviceController::class, 'heartbeat']);

    // Mobile Guest Token & Authentication
    Route::post('/auth/guest-token', [AuthController::class, 'guestToken']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/user', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
    });

    // Media Catalog & Streams
    Route::get('/featured', [MediaController::class, 'featured']);
    Route::get('/trending', [MediaController::class, 'trending']);
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

    // Ad & Telemetry Analytics
    Route::post('/telemetry/ad-event', [TelemetryController::class, 'recordAdEvent']);
    Route::post('/telemetry/error', [TelemetryController::class, 'recordAppError']);
});