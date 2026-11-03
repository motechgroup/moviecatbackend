<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DeviceController extends Controller
{
    public function heartbeat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => 'required|string|max:128',
            'username' => 'nullable|string|max:64',
            'platform' => 'nullable|string|max:32',
            'device_name' => 'nullable|string|max:128',
            'os_version' => 'nullable|string|max:64',
            'app_version' => 'nullable|string|max:32',
        ]);

        $deviceId = $validated['device_id'];
        $username = $validated['username'] ?? null;
        $ip = $request->ip();
        $registered = false;
        $deviceUsername = $username;

        try {
            $device = Device::where('device_id', $deviceId)->first();

            if ($device) {
                // If device already has username and none was passed, keep it; otherwise update
                $finalUsername = !empty($username) ? $username : ($device->username ?: 'MovieCat #' . strtoupper(substr(md5($deviceId), 0, 4)));
                $device->update([
                    'username' => $finalUsername,
                    'platform' => $validated['platform'] ?? $device->platform,
                    'device_name' => $validated['device_name'] ?? $device->device_name,
                    'os_version' => $validated['os_version'] ?? $device->os_version,
                    'app_version' => $validated['app_version'] ?? $device->app_version,
                    'ip_address' => $ip,
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

        return response()->json([
            'success' => true,
            'device' => [
                'deviceId' => $deviceId,
                'username' => $deviceUsername,
                'registered' => $registered,
            ],
            'ads' => [
                'enabled' => $adsEnabled,
            ],
        ]);
    }
}
