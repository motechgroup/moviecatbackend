<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

// Named login fallback for Laravel's Authenticate middleware
Route::get('/login', fn() => redirect()->route('admin.login'))->name('login');

// Public Root Redirect
Route::get('/', function () {
    return redirect()->route('admin.login');
})->name('home');

// Admin Portal Routes
Route::prefix('admin')->group(function () {
    // Unauthenticated Admin Auth Routes (with strict brute-force rate limiting)
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Protected Admin Endpoints (Requires valid session AND admin role)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // Admin Profile Management
        Route::get('/profile', [DashboardController::class, 'profile'])->name('admin.profile');
        Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('admin.profile.update');
        Route::post('/profile/password', [DashboardController::class, 'updatePassword'])->name('admin.profile.password');

        // Active Devices Fleet
        Route::get('/devices', [DashboardController::class, 'devices'])->name('admin.devices.index');
        Route::get('/traffic', [DashboardController::class, 'traffic'])->name('admin.traffic.index');
        Route::get('/devices/{deviceId}', [DashboardController::class, 'showDevice'])->name('admin.devices.show');
        Route::post('/devices/{deviceId}/rename', [DashboardController::class, 'renameDevice'])->name('admin.devices.rename');

        // AdMob Monetization
        Route::get('/admob', [DashboardController::class, 'admob'])->name('admin.admob.index');
        Route::post('/admob/toggle', [DashboardController::class, 'toggleAdMob'])->name('admin.admob.toggle');
        Route::post('/admob/toggle-moviecat-ads', [DashboardController::class, 'toggleMovieCatAds'])->name('admin.admob.toggle-moviecat-ads');
        Route::post('/admob/moviecat-ads/create', [DashboardController::class, 'createMovieCatAd'])->name('admin.admob.moviecat-ads.create');
        Route::delete('/admob/moviecat-ads/{id}', [DashboardController::class, 'deleteMovieCatAd'])->name('admin.admob.moviecat-ads.delete');
        Route::post('/admob/settings', [DashboardController::class, 'updateAdmobSettings'])->name('admin.admob.settings');

        // Watch Progress & Sync Telemetry
        Route::get('/watch-history', [DashboardController::class, 'watchHistory'])->name('admin.sync.index');

        // Downloads & Offline Telemetry
        Route::get('/downloads', [DashboardController::class, 'downloads'])->name('admin.downloads.index');
        Route::post('/downloads/toggle', [DashboardController::class, 'toggleDownloads'])->name('admin.downloads.toggle');
        Route::post('/wipe-mock-data', [DashboardController::class, 'wipeMockData'])->name('admin.wipe-mock');

        // Client App Error Telemetry Logs
        Route::get('/errors', [DashboardController::class, 'errors'])->name('admin.errors.index');
        Route::get('/errors/export', [DashboardController::class, 'exportErrors'])->name('admin.errors.export');
        Route::post('/errors/resolve-all', [DashboardController::class, 'resolveAllErrors'])->name('admin.errors.resolveAll');
        Route::post('/errors/{id}/resolve', [DashboardController::class, 'resolveError'])->name('admin.errors.resolve');
        Route::delete('/errors/{id}', [DashboardController::class, 'deleteError'])->name('admin.errors.delete');
        Route::post('/errors/clear-resolved', [DashboardController::class, 'clearResolvedErrors'])->name('admin.errors.clear');

        // Revenue & Subscriptions Management
        Route::get('/revenue', [\App\Http\Controllers\Admin\RevenueController::class, 'index'])->name('admin.revenue.index');
        Route::get('/searches', [DashboardController::class, 'searches'])->name('admin.searches.index');
        Route::post('/revenue/{id}/activate', [\App\Http\Controllers\Admin\RevenueController::class, 'activate'])->name('admin.revenue.activate');
                Route::post('/revenue/{id}/cancel', [\App\Http\Controllers\Admin\RevenueController::class, 'cancel'])->name('admin.revenue.cancel');

        // Remote App Settings
        Route::get('/settings', [DashboardController::class, 'settings'])->name('admin.settings.index');
        Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('admin.settings.update');
    });
});
