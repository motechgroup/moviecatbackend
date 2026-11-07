<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Device;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DeviceController extends Controller
{
    private static array $countryNames = [
        'US' => 'United States',
        'GB' => 'United Kingdom',
        'CA' => 'Canada',
        'AU' => 'Australia',
        'DE' => 'Germany',
        'FR' => 'France',
        'KE' => 'Kenya',
        'NG' => 'Nigeria',
        'ZA' => 'South Africa',
        'IN' => 'India',
        'PH' => 'Philippines',
        'BR' => 'Brazil',
        'MX' => 'Mexico',
        'ES' => 'Spain',
        'IT' => 'Italy',
        'NL' => 'Netherlands',
        'SE' => 'Sweden',
        'JP' => 'Japan',
        'KR' => 'South Korea',
        'AE' => 'United Arab Emirates',
        'SA' => 'Saudi Arabia',
        'EG' => 'Egypt',
        'GH' => 'Ghana',
        'TZ' => 'Tanzania',
        'UG' => 'Uganda',
    ];

    public function heartbeat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => 'required|string|max:128',
            'username' => 'nullable|string|max:64',
            'platform' => 'nullable|string|max:32',
            'device_name' => 'nullable|string|max:128',
            'os_version' => 'nullable|string|max:64',
            'app_version' => 'nullable|string|max:32',
            'country' => 'nullable|string|max:64',
            'country_code' => 'nullable|string|max:8',
            'city' => 'nullable|string|max:64',
            'timezone' => 'nullable|string|max:64',
        ]);

        $deviceId = $validated['device_id'];
        $username = $validated['username'] ?? null;
        $ip = $request->ip();

        // 1. Detect Country from Cloudflare header or payload
        $cfCountry = strtoupper(trim((string)$request->header('CF-IPCountry')));
        $passedCountryCode = strtoupper(trim((string)($validated['country_code'] ?? '')));
        $countryCode = (strlen($cfCountry) === 2 && $cfCountry !== 'XX') ? $cfCountry : (strlen($passedCountryCode) === 2 ? $passedCountryCode : null);

        // Fallback: Infer from timezone if countryCode still null
        if (!$countryCode && !empty($validated['timezone'])) {
            $tz = $validated['timezone'];
            if (str_contains($tz, 'America/')) $countryCode = 'US';
            elseif (str_contains($tz, 'Europe/London')) $countryCode = 'GB';
            elseif (str_contains($tz, 'Europe/Berlin') || str_contains($tz, 'Europe/Paris')) $countryCode = 'DE';
            elseif (str_contains($tz, 'Africa/Nairobi')) $countryCode = 'KE';
            elseif (str_contains($tz, 'Africa/Lagos')) $countryCode = 'NG';
            elseif (str_contains($tz, 'Asia/Kolkata')) $countryCode = 'IN';
        }

        $countryName = $validated['country'] ?? (self::$countryNames[$countryCode] ?? ($countryCode ? $countryCode : null));
        $city = $validated['city'] ?? $request->header('CF-IPCity');

        $registered = false;
        $deviceUsername = $username;

        try {
            $device = Device::where('device_id', $deviceId)->first();

            if ($device) {
                $finalUsername = !empty($username) ? $username : ($device->username ?: 'MovieCat #' . strtoupper(substr(md5($deviceId), 0, 4)));
                $device->update([
                    'username' => $finalUsername,
                    'platform' => $validated['platform'] ?? $device->platform,
                    'device_name' => $validated['device_name'] ?? $device->device_name,
                    'os_version' => $validated['os_version'] ?? $device->os_version,
                    'app_version' => $validated['app_version'] ?? $device->app_version,
                    'ip_address' => $ip,
                    'country' => $countryName ?: $device->country,
                    'country_code' => $countryCode ?: $device->country_code,
                    'city' => $city ?: $device->city,
                    'last_active_at' => now(),
                    'sessions_count' => $device->sessions_count + 1,
                ]);
                $deviceUsername = $finalUsername;
            } else {
                $finalUsername = !empty($username) ? $username : 'MovieCat #' . strtoupper(substr(md5($deviceId), 0, 4));
                $device = Device::create([
                    'device_id' => $deviceId,
                    'username' => $finalUsername,
                    'platform' => $validated['platform'] ?? 'android',
                    'device_name' => $validated['device_name'] ?? 'Mobile Device',
                    'os_version' => $validated['os_version'] ?? null,
                    'app_version' => $validated['app_version'] ?? '1.0.0',
                    'ip_address' => $ip,
                    'country' => $countryName ?: 'United States',
                    'country_code' => $countryCode ?: 'US',
                    'city' => $city ?: null,
                    'last_active_at' => now(),
                    'sessions_count' => 1,
                ]);
                $deviceUsername = $finalUsername;
            }
            $registered = true;
        } catch (Throwable) {
            $registered = false;
        }

        $adsEnabled = (bool)AppSetting::get('ads_enabled', true);
        $downloadsEnabled = (bool)AppSetting::get('feature_downloads_enabled', true);

        $vaultSubEnabled = (bool)AppSetting::get('vault_subscription_enabled', false);
        $activeSub = Subscription::getActiveFor($deviceId, $deviceUsername);

        return response()->json([
            'success' => true,
            'device' => [
                'deviceId' => $deviceId,
                'username' => $deviceUsername,
                'country' => $countryName,
                'countryCode' => $countryCode,
                'registered' => $registered,
            ],
            'ads' => [
                'enabled' => $adsEnabled,
            ],
            'features' => [
                'downloads' => $downloadsEnabled,
            ],
            'subscription' => [
                'enabled' => $vaultSubEnabled,
                'is_active' => $activeSub ? true : false,
                'expires_at' => $activeSub ? $activeSub->expires_at->toIso8601String() : null,
            ],
        ]);
    }
}
