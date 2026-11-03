<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdEvent;
use App\Models\AppError;
use App\Models\AppSetting;
use App\Models\Bookmark;
use App\Models\Device;
use App\Models\DownloadEvent;
use App\Models\WatchProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Throwable;

class DashboardController extends Controller
{
    private function formatSeconds(int $seconds): string
    {
        if ($seconds <= 0) return '0 min';
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        return "{$minutes}m";
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) return '0 MB';
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        return number_format($bytes / 1024, 0) . ' KB';
    }

    // 1. Overview Dashboard with Analytics
    public function index(): View
    {
        try {
            $activeThreshold = now()->subMinutes(15);
            $recentActiveThreshold = now()->subHours(24);

            $totalWatchSeconds = (int)WatchProgress::sum('current_time');
            $totalDownloadBytes = Schema::hasTable('download_events')
                ? (int)DownloadEvent::where('status', 'completed')->sum('file_size_bytes')
                : 0;
            $totalDownloads = Schema::hasTable('download_events') ? DownloadEvent::count() : 0;

            $totalDevicesCount = Device::count();

            $stats = [
                'totalDevices' => $totalDevicesCount,
                'activeNowDevices' => Device::where('last_active_at', '>=', $activeThreshold)->count(),
                'activeTodayDevices' => Device::where('last_active_at', '>=', $recentActiveThreshold)->count(),
                'androidDevices' => Device::where('platform', 'android')->count(),
                'iosDevices' => Device::where('platform', 'ios')->count(),
                'totalProgress' => WatchProgress::count(),
                'completedWatches' => WatchProgress::where('completed', true)->count(),
                'totalBookmarks' => Bookmark::count(),
                'adImpressions' => AdEvent::where('event_type', 'impression')->count(),
                'unresolvedErrors' => AppError::where('resolved', false)->count(),
                'totalErrors' => AppError::count(),
                'totalCountries' => (Schema::hasColumn('devices', 'country_code') ? Device::distinct()->count('country_code') : 0),
                'totalWatchTime' => $this->formatSeconds($totalWatchSeconds),
                'totalDownloads' => $totalDownloads,
                'totalDataTransferred' => $this->formatBytes($totalDownloadBytes),
            ];

            $recentDevices = Device::orderByDesc('last_active_at')->take(6)->get();
            $recentSyncs = WatchProgress::with('device')->orderByDesc('last_watched_at')->take(6)->get();
            $recentErrors = AppError::with('device')->orderByDesc('created_at')->take(5)->get();

            // Country Analytics with Percentage Breakdown & Watch time
            $topCountries = Device::select('country', 'country_code', DB::raw('count(*) as count'), DB::raw('sum(total_watch_seconds) as watch_seconds'))
                ->whereNotNull('country')
                ->groupBy('country', 'country_code')
                ->orderByDesc('count')
                ->take(6)
                ->get()
                ->map(function ($c) use ($totalDevicesCount) {
                    $c->percentage = $totalDevicesCount > 0 ? round(($c->count / $totalDevicesCount) * 100, 1) : 0;
                    $c->formatted_watch_time = $this->formatSeconds((int)$c->watch_seconds);
                    return $c;
                });

            // Most Watched Movies & TV Shows
            $mostWatchedMovies = WatchProgress::select('title', 'poster', 'media_id', DB::raw('count(*) as count'), DB::raw('sum(current_time) as total_seconds'))
                ->where('media_type', 'movie')
                ->groupBy('title', 'poster', 'media_id')
                ->orderByDesc('count')
                ->take(5)
                ->get()
                ->map(function ($item) {
                    $item->formatted_time = $this->formatSeconds((int)$item->total_seconds);
                    return $item;
                });

            $mostWatchedSeries = WatchProgress::select('title', 'poster', 'media_id', DB::raw('count(*) as count'), DB::raw('sum(current_time) as total_seconds'))
                ->where('media_type', 'tv')
                ->groupBy('title', 'poster', 'media_id')
                ->orderByDesc('count')
                ->take(5)
                ->get()
                ->map(function ($item) {
                    $item->formatted_time = $this->formatSeconds((int)$item->total_seconds);
                    return $item;
                });

            // Most Downloaded Media
            $mostDownloaded = Schema::hasTable('download_events')
                ? DownloadEvent::select('title', 'poster', 'media_id', 'media_type', DB::raw('count(*) as count'), DB::raw('sum(file_size_bytes) as total_bytes'))
                    ->where('status', 'completed')
                    ->groupBy('title', 'poster', 'media_id', 'media_type')
                    ->orderByDesc('count')
                    ->take(5)
                    ->get()
                    ->map(function ($item) {
                        $item->formatted_size = $this->formatBytes((int)$item->total_bytes);
                        return $item;
                    })
                : collect();

        } catch (Throwable) {
            $stats = [
                'totalDevices' => 0, 'activeNowDevices' => 0, 'activeTodayDevices' => 0,
                'androidDevices' => 0, 'iosDevices' => 0, 'totalProgress' => 0,
                'completedWatches' => 0, 'totalBookmarks' => 0, 'adImpressions' => 0,
                'unresolvedErrors' => 0, 'totalErrors' => 0, 'totalCountries' => 0,
                'totalWatchTime' => '0 min', 'totalDownloads' => 0, 'totalDataTransferred' => '0 MB',
            ];
            $recentDevices = collect();
            $recentSyncs = collect();
            $recentErrors = collect();
            $topCountries = collect();
            $mostWatchedMovies = collect();
            $mostWatchedSeries = collect();
            $mostDownloaded = collect();
        }

        $adsEnabled = (bool)AppSetting::get('ads_enabled', true);

        return view('admin.dashboard', compact(
            'stats',
            'recentDevices',
            'recentSyncs',
            'recentErrors',
            'topCountries',
            'mostWatchedMovies',
            'mostWatchedSeries',
            'mostDownloaded',
            'adsEnabled'
        ));
    }

    // 2. Active Devices Fleet Page
    public function devices(Request $request): View
    {
        $activeThreshold = now()->subMinutes(15);
        $recentActiveThreshold = now()->subHours(24);

        try {
            $stats = [
                'total' => Device::count(),
                'activeNow' => Device::where('last_active_at', '>=', $activeThreshold)->count(),
                'activeToday' => Device::where('last_active_at', '>=', $recentActiveThreshold)->count(),
                'android' => Device::where('platform', 'android')->count(),
                'ios' => Device::where('platform', 'ios')->count(),
                'countriesCount' => (Schema::hasColumn('devices', 'country_code') ? Device::distinct()->count('country_code') : 0),
            ];

            $query = Device::withCount(['watchProgress', 'bookmarks']);

            if ($search = $request->query('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                      ->orWhere('device_id', 'like', "%{$search}%")
                      ->orWhere('device_name', 'like', "%{$search}%")
                      ->orWhere('country', 'like', "%{$search}%")
                      ->orWhere('country_code', 'like', "%{$search}%")
                      ->orWhere('ip_address', 'like', "%{$search}%");
                });
            }

            if ($platform = $request->query('platform')) {
                $query->where('platform', $platform);
            }

            if ($country = $request->query('country')) {
                $query->where(function ($q) use ($country) {
                    $q->where('country_code', $country)
                      ->orWhere('country', $country);
                });
            }

            if ($request->query('status') === 'online') {
                $query->where('last_active_at', '>=', $activeThreshold);
            }

            $devices = $query->orderByDesc('last_active_at')->paginate(15)->withQueryString();

            $availableCountries = Device::select('country', 'country_code', DB::raw('count(*) as count'))
                ->whereNotNull('country_code')
                ->groupBy('country', 'country_code')
                ->orderByDesc('count')
                ->get();
        } catch (Throwable) {
            $stats = [
                'total' => 0, 'activeNow' => 0, 'activeToday' => 0,
                'android' => 0, 'ios' => 0, 'countriesCount' => 0,
            ];
            $devices = new LengthAwarePaginator([], 0, 15);
            $availableCountries = collect();
        }

        return view('admin.devices', compact('devices', 'stats', 'availableCountries'));
    }

    // 3. Device Details Page
    public function showDevice(string $deviceId): View
    {
        try {
            $device = Device::where('device_id', $deviceId)->firstOrFail();
            $watchHistory = WatchProgress::where('device_id', $deviceId)->orderByDesc('last_watched_at')->get();
            $bookmarks = Bookmark::where('device_id', $deviceId)->orderByDesc('created_at')->get();
            $adEvents = AdEvent::where('device_id', $deviceId)->orderByDesc('created_at')->take(50)->get();
            $errors = AppError::where('device_id', $deviceId)->orderByDesc('created_at')->take(50)->get();
            $downloads = Schema::hasTable('download_events')
                ? DownloadEvent::where('device_id', $deviceId)->orderByDesc('created_at')->take(50)->get()
                : collect();
        } catch (Throwable) {
            abort(404, 'Device not found.');
        }

        return view('admin.device-details', compact('device', 'watchHistory', 'bookmarks', 'adEvents', 'errors', 'downloads'));
    }

    // 4. Downloads & Offline Analytics Page
    public function downloads(Request $request): View
    {
        try {
            $query = DownloadEvent::with('device');

            if ($search = $request->query('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('device_id', 'like', "%{$search}%")
                      ->orWhereHas('device', function ($dq) use ($search) {
                          $dq->where('username', 'like', "%{$search}%");
                      });
                });
            }

            if ($type = $request->query('type')) {
                $query->where('media_type', $type);
            }

            if ($status = $request->query('status')) {
                $query->where('status', $status);
            }

            $downloads = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

            $totalBytes = (int)DownloadEvent::where('status', 'completed')->sum('file_size_bytes');

            $stats = [
                'total' => DownloadEvent::count(),
                'completed' => DownloadEvent::where('status', 'completed')->count(),
                'offlineWatched' => DownloadEvent::where('status', 'offline_watched')->count(),
                'totalData' => $this->formatBytes($totalBytes),
                'uniqueDevices' => DownloadEvent::distinct('device_id')->count('device_id'),
            ];

            // Top Downloaded Movies
            $topMovies = DownloadEvent::select('title', 'poster', 'media_id', DB::raw('count(*) as count'), DB::raw('sum(file_size_bytes) as bytes'))
                ->where('media_type', 'movie')
                ->where('status', 'completed')
                ->groupBy('title', 'poster', 'media_id')
                ->orderByDesc('count')
                ->take(6)
                ->get()
                ->map(function ($item) {
                    $item->formatted_size = $this->formatBytes((int)$item->bytes);
                    return $item;
                });

            // Top Downloaded TV Series
            $topSeries = DownloadEvent::select('title', 'poster', 'media_id', DB::raw('count(*) as count'), DB::raw('sum(file_size_bytes) as bytes'))
                ->where('media_type', 'tv')
                ->where('status', 'completed')
                ->groupBy('title', 'poster', 'media_id')
                ->orderByDesc('count')
                ->take(6)
                ->get()
                ->map(function ($item) {
                    $item->formatted_size = $this->formatBytes((int)$item->bytes);
                    return $item;
                });

        } catch (Throwable) {
            $downloads = new LengthAwarePaginator([], 0, 20);
            $stats = ['total' => 0, 'completed' => 0, 'offlineWatched' => 0, 'totalData' => '0 MB', 'uniqueDevices' => 0];
            $topMovies = collect();
            $topSeries = collect();
        }

        return view('admin.downloads', compact('downloads', 'stats', 'topMovies', 'topSeries'));
    }

    // 5. AdMob Monetization Page
    public function admob(): View
    {
        $adsEnabled = (bool)AppSetting::get('ads_enabled', true);

        $settings = [
            'ads_enabled' => $adsEnabled,
            'admob_android_banner_id' => AppSetting::get('admob_android_banner_id', 'ca-app-pub-6725687905795161/2199152801'),
            'admob_android_interstitial_id' => AppSetting::get('admob_android_interstitial_id', 'ca-app-pub-6725687905795161/7159388334'),
            'admob_android_rewarded_id' => AppSetting::get('admob_android_rewarded_id', 'ca-app-pub-3940256099942544/5224354917'),
            'admob_ios_banner_id' => AppSetting::get('admob_ios_banner_id', 'ca-app-pub-3940256099942544/2934735716'),
            'admob_ios_interstitial_id' => AppSetting::get('admob_ios_interstitial_id', 'ca-app-pub-3940256099942544/4411468910'),
            'admob_ios_rewarded_id' => AppSetting::get('admob_ios_rewarded_id', 'ca-app-pub-3940256099942544/1712485313'),
            'interstitial_frequency' => AppSetting::get('interstitial_frequency', 4),
            'banner_interval' => AppSetting::get('banner_interval', 30),
        ];

        try {
            $adStats = [
                'impressions' => AdEvent::where('event_type', 'impression')->count(),
                'clicks' => AdEvent::where('event_type', 'click')->count(),
                'failed' => AdEvent::where('event_type', 'failed')->count(),
            ];
        } catch (Throwable) {
            $adStats = ['impressions' => 0, 'clicks' => 0, 'failed' => 0];
        }

        return view('admin.admob', compact('adsEnabled', 'settings', 'adStats'));
    }

    public function toggleAdMob(Request $request): RedirectResponse
    {
        $current = (bool)AppSetting::get('ads_enabled', true);
        $newVal = !$current;
        AppSetting::set('ads_enabled', $newVal, 'ads');

        $statusMsg = $newVal
            ? 'AdMob Monetization ENABLED. Mobile devices will display ads in real-time.'
            : 'AdMob Monetization DISABLED. All ads are silenced across the mobile app in real-time.';

        return back()->with('success', $statusMsg);
    }

    public function updateAdmobSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'admob_android_banner_id' => 'nullable|string',
            'admob_android_interstitial_id' => 'nullable|string',
            'admob_android_rewarded_id' => 'nullable|string',
            'admob_ios_banner_id' => 'nullable|string',
            'admob_ios_interstitial_id' => 'nullable|string',
            'admob_ios_rewarded_id' => 'nullable|string',
            'interstitial_frequency' => 'nullable|integer|min:1|max:20',
            'banner_interval' => 'nullable|integer|min:10|max:120',
        ]);

        foreach ($data as $k => $v) {
            if ($v !== null) AppSetting::set($k, $v, 'ads');
        }

        return back()->with('success', 'AdMob unit configurations saved successfully!');
    }

    // 6. Watch History Telemetry Page
    public function watchHistory(Request $request): View
    {
        try {
            $query = WatchProgress::with('device');

            if ($search = $request->query('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('device_id', 'like', "%{$search}%")
                      ->orWhereHas('device', function ($dq) use ($search) {
                          $dq->where('username', 'like', "%{$search}%");
                      });
                });
            }

            if ($type = $request->query('type')) {
                $query->where('media_type', $type);
            }

            $history = $query->orderByDesc('last_watched_at')->paginate(20)->withQueryString();

            $totalSeconds = (int)WatchProgress::sum('current_time');

            $stats = [
                'totalCheckpoints' => WatchProgress::count(),
                'completed' => WatchProgress::where('completed', true)->count(),
                'movies' => WatchProgress::where('media_type', 'movie')->count(),
                'series' => WatchProgress::where('media_type', 'tv')->count(),
                'totalWatchTime' => $this->formatSeconds($totalSeconds),
            ];
        } catch (Throwable) {
            $history = new LengthAwarePaginator([], 0, 20);
            $stats = ['totalCheckpoints' => 0, 'completed' => 0, 'movies' => 0, 'series' => 0, 'totalWatchTime' => '0 min'];
        }

        return view('admin.watch-history', compact('history', 'stats'));
    }

    // 7. Client App Error Telemetry Logs Page
    public function errors(Request $request): View
    {
        try {
            $query = AppError::with('device');

            if ($search = $request->query('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('error_message', 'like', "%{$search}%")
                      ->orWhere('screen', 'like', "%{$search}%")
                      ->orWhere('device_id', 'like', "%{$search}%")
                      ->orWhereHas('device', function ($dq) use ($search) {
                          $dq->where('username', 'like', "%{$search}%");
                      });
                });
            }

            if ($severity = $request->query('severity')) {
                $query->where('severity', $severity);
            }

            if ($screen = $request->query('screen')) {
                $query->where('screen', $screen);
            }

            if ($request->query('status') === 'unresolved') {
                $query->where('resolved', false);
            } elseif ($request->query('status') === 'resolved') {
                $query->where('resolved', true);
            }

            $errors = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

            $stats = [
                'total' => AppError::count(),
                'unresolved' => AppError::where('resolved', false)->count(),
                'fatal' => AppError::where('severity', 'fatal')->count(),
                'last24h' => AppError::where('created_at', '>=', now()->subHours(24))->count(),
            ];

            $screens = AppError::select('screen', DB::raw('count(*) as count'))
                ->whereNotNull('screen')
                ->groupBy('screen')
                ->orderByDesc('count')
                ->get();
        } catch (Throwable) {
            $errors = new LengthAwarePaginator([], 0, 20);
            $stats = ['total' => 0, 'unresolved' => 0, 'fatal' => 0, 'last24h' => 0];
            $screens = collect();
        }

        return view('admin.errors', compact('errors', 'stats', 'screens'));
    }

    public function resolveError(string $id): RedirectResponse
    {
        $error = AppError::findOrFail($id);
        $error->resolved = !$error->resolved;
        $error->save();

        $msg = $error->resolved ? 'Error marked as resolved.' : 'Error reopened.';
        return back()->with('success', $msg);
    }

    public function deleteError(string $id): RedirectResponse
    {
        $error = AppError::findOrFail($id);
        $error->delete();

        return back()->with('success', 'Error log deleted.');
    }

    public function clearResolvedErrors(): RedirectResponse
    {
        $deleted = AppError::where('resolved', true)->delete();
        return back()->with('success', "Cleared {$deleted} resolved error logs.");
    }

    // 8. Wipe Mock & Test Data Button
    public function wipeMockData(Request $request): RedirectResponse
    {
        try {
            Schema::disableForeignKeyConstraints();
            Device::truncate();
            WatchProgress::truncate();
            Bookmark::truncate();
            AdEvent::truncate();
            AppError::truncate();
            if (Schema::hasTable('download_events')) {
                DownloadEvent::truncate();
            }
            Schema::enableForeignKeyConstraints();

            return back()->with('success', 'All mock devices, watch checkpoints, bookmarks, ad telemetry, and error logs wiped cleanly! Platform ready for live user traffic.');
        } catch (Throwable $e) {
            return back()->with('error', 'Wipe failed: ' . $e->getMessage());
        }
    }

    // 9. Remote App Settings Page
    public function settings(): View
    {
        $settings = [
            'announcement_active' => (bool)AppSetting::get('announcement_active', true),
            'announcement_text' => AppSetting::get('announcement_text', 'MovieCat v1.0 is live! Seamless streaming & bulk downloads enabled.'),
            'min_app_version' => AppSetting::get('min_app_version', '1.0.0'),
            'force_update' => (bool)AppSetting::get('force_update', false),
            'maintenance_mode' => (bool)AppSetting::get('maintenance_mode', false),
            'feature_bulk_download' => (bool)AppSetting::get('feature_bulk_download', true),
            'feature_similar_series' => (bool)AppSetting::get('feature_similar_series', true),
            'feature_cloud_sync' => (bool)AppSetting::get('feature_cloud_sync', true),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'announcement_text' => 'nullable|string',
            'min_app_version' => 'nullable|string',
        ]);

        AppSetting::set('announcement_active', (bool)$request->has('announcement_active'), 'general');
        AppSetting::set('force_update', (bool)$request->has('force_update'), 'general');
        AppSetting::set('maintenance_mode', (bool)$request->has('maintenance_mode'), 'general');
        AppSetting::set('feature_bulk_download', (bool)$request->has('feature_bulk_download'), 'features');
        AppSetting::set('feature_similar_series', (bool)$request->has('feature_similar_series'), 'features');
        AppSetting::set('feature_cloud_sync', (bool)$request->has('feature_cloud_sync'), 'features');

        if (!empty($data['announcement_text'])) {
            AppSetting::set('announcement_text', $data['announcement_text'], 'general');
            AppSetting::set('announcement_banner', [
                'active' => (bool)$request->has('announcement_active'),
                'text' => $data['announcement_text'],
                'type' => 'info',
            ], 'general');
        }

        if (!empty($data['min_app_version'])) {
            AppSetting::set('min_app_version', $data['min_app_version'], 'general');
        }

        return back()->with('success', 'Remote application settings updated successfully!');
    }
}
