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

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Protected Admin Pages
    Route::middleware(['auth'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // Active Devices Fleet
        Route::get('/devices', [DashboardController::class, 'devices'])->name('admin.devices.index');
        Route::get('/devices/{deviceId}', [DashboardController::class, 'showDevice'])->name('admin.devices.show');

        // AdMob Monetization
        Route::get('/admob', [DashboardController::class, 'admob'])->name('admin.admob.index');
        Route::post('/admob/toggle', [DashboardController::class, 'toggleAdMob'])->name('admin.admob.toggle');
        Route::post('/admob/settings', [DashboardController::class, 'updateAdmobSettings'])->name('admin.admob.settings');

        // Watch Progress & Sync Telemetry
        Route::get('/watch-history', [DashboardController::class, 'watchHistory'])->name('admin.sync.index');

        // Downloads & Offline Telemetry
        Route::get('/downloads', [DashboardController::class, 'downloads'])->name('admin.downloads.index');
        Route::post('/wipe-mock-data', [DashboardController::class, 'wipeMockData'])->name('admin.wipe-mock');

        // Client App Error Telemetry Logs
        Route::get('/errors', [DashboardController::class, 'errors'])->name('admin.errors.index');
        Route::post('/errors/{id}/resolve', [DashboardController::class, 'resolveError'])->name('admin.errors.resolve');
        Route::delete('/errors/{id}', [DashboardController::class, 'deleteError'])->name('admin.errors.delete');
        Route::post('/errors/clear-resolved', [DashboardController::class, 'clearResolvedErrors'])->name('admin.errors.clear');

        // Remote App Settings
        Route::get('/settings', [DashboardController::class, 'settings'])->name('admin.settings.index');
        Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('admin.settings.update');
    });
});
