<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $platform = strtolower($request->header("X-Platform", $request->query("platform", "android")));
        $deviceId = $request->header("X-Device-Id", $request->query("device_id"));
        $username = $request->header("X-Username", $request->query("username"));

        $bannerId = $platform === "ios"
            ? AppSetting::get("admob_ios_banner_id", "ca-app-pub-3940256099942544/2934735716")
            : AppSetting::get("admob_android_banner_id", "ca-app-pub-3940256099942544/6300978111");

        $interstitialId = $platform === "ios"
            ? AppSetting::get("admob_ios_interstitial_id", "ca-app-pub-3940256099942544/4411468910")
            : AppSetting::get("admob_android_interstitial_id", "ca-app-pub-3940256099942544/1033173712");

        $rewardedId = $platform === "ios"
            ? AppSetting::get("admob_ios_rewarded_id", "ca-app-pub-3940256099942544/1712485313")
            : AppSetting::get("admob_android_rewarded_id", "ca-app-pub-3940256099942544/5224354917");

        $price = (float)AppSetting::get("vault_subscription_price", 100);
        $currency = (string)AppSetting::get("vault_subscription_currency", "KES");
        $periodDays = (int)AppSetting::get("vault_subscription_period_days", 30);
        $packageName = (string)AppSetting::get("vault_subscription_package_name", "MovieCat Vault VIP");

        $activeSub = Subscription::getActiveFor($deviceId, $username);

        $gateways = [];
        if (AppSetting::get("gateway_mpesa_enabled", true)) {
            $gateways[] = [
                "id" => "mpesa",
                "name" => "M-Pesa (Direct STK Push)",
                "subtitle" => "Instant Safaricom PIN prompt on your phone",
                "badge" => "Instant",
                "icon" => "phone-portrait-outline",
            ];
        }
        if (AppSetting::get("gateway_payhero_enabled", true)) {
            $gateways[] = [
                "id" => "payhero",
                "name" => "PayHero",
                "subtitle" => "Fast M-Pesa & Mobile Money checkout",
                "badge" => "Fast",
                "icon" => "flash-outline",
            ];
        }
        if (AppSetting::get("gateway_flutterwave_enabled", true)) {
            $gateways[] = [
                "id" => "flutterwave",
                "name" => "Flutterwave (East Africa)",
                "subtitle" => "Kenya, Uganda, Tanzania, Rwanda • M-Pesa, Airtel, Card",
                "badge" => "EA Regional",
                "icon" => "globe-outline",
            ];
        }

        return response()->json([
            "success" => true,
            "app" => [
                "name" => "MovieCat",
                "version" => "1.0.0",
                "minVersion" => AppSetting::get("min_app_version", "1.0.0"),
                "forceUpdate" => AppSetting::get("force_update", false),
                "maintenance" => AppSetting::get("maintenance_mode", false),
            ],
            "ads" => [
                "enabled" => AppSetting::get("ads_enabled", true),
                "bannerId" => $bannerId,
                "interstitialId" => $interstitialId,
                "rewardedId" => $rewardedId,
                "interstitialFrequency" => AppSetting::get("interstitial_frequency", 4),
                "bannerInterval" => AppSetting::get("banner_interval", 30),
            ],
            "announcement" => AppSetting::get("announcement_banner", [
                "active" => false,
                "text" => "",
                "type" => "info",
            ]),
            "features" => [
                "downloads" => (bool)AppSetting::get("feature_downloads_enabled", true),
                "bulkDownload" => AppSetting::get("feature_bulk_download", true),
                "similarSeries" => AppSetting::get("feature_similar_series", true),
                "cloudSync" => AppSetting::get("feature_cloud_sync", true),
            ],
            "subscription" => [
                "enabled" => (bool)AppSetting::get("vault_subscription_enabled", false),
                "package" => [
                    "id" => "vault_vip",
                    "name" => $packageName,
                    "price" => $price,
                    "currency" => $currency,
                    "formatted_price" => "{$currency} {$price} / {$periodDays} Days",
                    "period_days" => $periodDays,
                    "description" => "Watch all downloaded movies & series offline in the Vault anytime, anywhere.",
                    "perks" => [
                        "Watch all downloaded movies & series offline",
                        "Zero buffering & zero mobile data used",
                        "Ad-free offline theater playback in the Vault",
                        "Full support for Phone, Tablet & Android TV",
                    ],
                ],
                "gateways" => $gateways,
                "user_status" => [
                    "is_active" => $activeSub ? true : false,
                    "expires_at" => $activeSub ? $activeSub->expires_at->toIso8601String() : null,
                    "plan_name" => $activeSub ? $activeSub->package_id : null,
                ],
            ],
        ]);
    }
}
