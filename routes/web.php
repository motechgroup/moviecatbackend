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

        // Media Catalog
        Route::get('/media', [DashboardController::class, 'media'])->name('admin.media.index');
        Route::post('/media', [DashboardController::class, 'storeMedia'])->name('admin.media.store');
        Route::delete('/media/{id}', [DashboardController::class, 'deleteMedia'])->name('admin.media.delete');

        // Watch Progress & Sync Telemetry
        Route::get('/watch-history', [DashboardController::class, 'watchHistory'])->name('admin.sync.index');

        // Remote App Settings
        Route::get('/settings', [DashboardController::class, 'settings'])->name('admin.settings.index');
        Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('admin.settings.update');
    });
});