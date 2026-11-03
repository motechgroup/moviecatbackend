<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $platform = strtolower($request->header('X-Platform', $request->query('platform', 'android')));

        $bannerId = $platform === 'ios'
            ? AppSetting::get('admob_ios_banner_id', 'ca-app-pub-3940256099942544/2934735716')
            : AppSetting::get('admob_android_banner_id', 'ca-app-pub-3940256099942544/6300978111');

        $interstitialId = $platform === 'ios'
            ? AppSetting::get('admob_ios_interstitial_id', 'ca-app-pub-3940256099942544/4411468910')
            : AppSetting::get('admob_android_interstitial_id', 'ca-app-pub-3940256099942544/1033173712');

        $rewardedId = $platform === 'ios'
            ? AppSetting::get('admob_ios_rewarded_id', 'ca-app-pub-3940256099942544/1712485313')
            : AppSetting::get('admob_android_rewarded_id', 'ca-app-pub-3940256099942544/5224354917');

        return response()->json([
            'success' => true,
            'app' => [
                'name' => 'MovieCat',
                'version' => '1.0.0',
                'minVersion' => AppSetting::get('min_app_version', '1.0.0'),
                'forceUpdate' => AppSetting::get('force_update', false),
                'maintenance' => AppSetting::get('maintenance_mode', false),
            ],
            'ads' => [
                'enabled' => AppSetting::get('ads_enabled', true),
                'bannerId' => $bannerId,
                'interstitialId' => $interstitialId,
                'rewardedId' => $rewardedId,
                'interstitialFrequency' => AppSetting::get('interstitial_frequency', 4), // every N actions
                'bannerInterval' => AppSetting::get('banner_interval', 30), // seconds
            ],
            'announcement' => AppSetting::get('announcement_banner', [
                'active' => false,
                'text' => '',
                'type' => 'info',
            ]),
            'features' => [
                'bulkDownload' => AppSetting::get('feature_bulk_download', true),
                'similarSeries' => AppSetting::get('feature_similar_series', true),
                'cloudSync' => AppSetting::get('feature_cloud_sync', true),
            ],
        ]);
    }
}