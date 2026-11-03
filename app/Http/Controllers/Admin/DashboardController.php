<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdEvent;
use App\Models\AppSetting;
use App\Models\Bookmark;
use App\Models\Device;
use App\Models\User;
use App\Models\WatchProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class DashboardController extends Controller
{
    // 1. Overview Dashboard
    public function index(): View
    {
        try {
            $activeThreshold = now()->subMinutes(15);
            $recentActiveThreshold = now()->subHours(24);

            $stats = [
                'totalDevices' => Device::count(),
                'activeNowDevices' => Device::where('last_active_at', '>=', $activeThreshold)->count(),
                'activeTodayDevices' => Device::where('last_active_at', '>=', $recentActiveThreshold)->count(),
                'androidDevices' => Device::where('platform', 'android')->count(),
                'iosDevices' => Device::where('platform', 'ios')->count(),
                'totalProgress' => WatchProgress::count(),
                'completedWatches' => WatchProgress::where('completed', true)->count(),
                'totalBookmarks' => Bookmark::count(),
                'adImpressions' => AdEvent::where('event_type', 'impression')->count(),
            ];

            $recentDevices = Device::orderByDesc('last_active_at')->take(6)->get();
            $recentSyncs = WatchProgress::with('device')->orderByDesc('last_watched_at')->take(8)->get();
        } catch (Throwable) {
            $stats = [
                'totalDevices' => 0, 'activeNowDevices' => 0, 'activeTodayDevices' => 0,
                'androidDevices' => 0, 'iosDevices' => 0, 'totalProgress' => 0,
                'completedWatches' => 0, 'totalBookmarks' => 0, 'adImpressions' => 0,
            ];
            $recentDevices = collect();
            $recentSyncs = collect();
        }

        $adsEnabled = (bool)AppSetting::get('ads_enabled', true);

        return view('admin.dashboard', compact('stats', 'recentDevices', 'recentSyncs', 'adsEnabled'));
    }

    // 2. Active Devices Fleet Page
    public function devices(Request $request): View
    {
        $activeThreshold = now()->subMinutes(15);
        $recentActiveThreshold = now()->subHours(24);

        $stats = [
            'total' => Device::count(),
            'activeNow' => Device::where('last_active_at', '>=', $activeThreshold)->count(),
            'activeToday' => Device::where('last_active_at', '>=', $recentActiveThreshold)->count(),
            'android' => Device::where('platform', 'android')->count(),
            'ios' => Device::where('platform', 'ios')->count(),
        ];

        $query = Device::withCount(['watchProgress', 'bookmarks']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('device_id', 'like', "%{$search}%")
                  ->orWhere('device_name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($platform = $request->query('platform')) {
            $query->where('platform', $platform);
        }

        if ($request->query('status') === 'online') {
            $query->where('last_active_at', '>=', $activeThreshold);
        }

        $devices = $query->orderByDesc('last_active_at')->paginate(15)->withQueryString();

        return view('admin.devices', compact('devices', 'stats'));
    }

    // 3. Device Details Page
    public function showDevice(string $deviceId): View
    {
        $device = Device::where('device_id', $deviceId)->firstOrFail();
        $watchHistory = WatchProgress::where('device_id', $deviceId)->orderByDesc('last_watched_at')->get();
        $bookmarks = Bookmark::where('device_id', $deviceId)->orderByDesc('created_at')->get();
        $adEvents = AdEvent::where('device_id', $deviceId)->orderByDesc('created_at')->take(50)->get();

        return view('admin.device-details', compact('device', 'watchHistory', 'bookmarks', 'adEvents'));
    }

    // 4. AdMob Monetization Page
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

        $adStats = [
            'impressions' => AdEvent::where('event_type', 'impression')->count(),
            'clicks' => AdEvent::where('event_type', 'click')->count(),
            'failed' => AdEvent::where('event_type', 'failed')->count(),
        ];

        return view('admin.admob', compact('adsEnabled', 'settings', 'adStats'));
    }

    // 5. Toggle AdMob (Master Switch)
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

    // 6. Update AdMob Unit IDs
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

    // 7. Watch History Telemetry Page
    public function watchHistory(Request $request): View
    {
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

        $stats = [
            'totalCheckpoints' => WatchProgress::count(),
            'completed' => WatchProgress::where('completed', true)->count(),
            'movies' => WatchProgress::where('media_type', 'movie')->count(),
            'series' => WatchProgress::where('media_type', 'tv')->count(),
        ];

        return view('admin.watch-history', compact('history', 'stats'));
    }

    // 8. Remote App Settings Page
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
            'announcement_active' => 'nullable|boolean',
            'announcement_text' => 'nullable|string',
            'min_app_version' => 'nullable|string',
            'force_update' => 'nullable|boolean',
            'maintenance_mode' => 'nullable|boolean',
            'feature_bulk_download' => 'nullable|boolean',
            'feature_similar_series' => 'nullable|boolean',
            'feature_cloud_sync' => 'nullable|boolean',
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
