<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'device_id' => 'nullable|string|max:128',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'device_id' => $validated['device_id'] ?? null,
            'role' => 'user',
        ]);

        $token = $user->createToken('moviecat-mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar,
                'is_premium' => $user->is_premium,
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'device_id' => 'nullable|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password',
            ], 401);
        }

        if (!empty($validated['device_id'])) {
            $user->device_id = $validated['device_id'];
            $user->save();
        }

        $token = $user->createToken('moviecat-mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar,
                'is_premium' => $user->is_premium,
            ],
        ]);
    }

    public function guestToken(Request $request): JsonResponse
    {
        $deviceId = $request->input('device_id') ?: 'mc_dev_' . Str::random(12);
        $passedUsername = $request->input('username');

        // Check if device exists or what username to use
        $device = Device::where('device_id', $deviceId)->first();
        $username = $passedUsername ?: ($device?->username ?: 'MovieCat #' . strtoupper(substr(md5($deviceId), 0, 4)));

        // Sync or create user profile
        $user = User::firstOrCreate(
            ['device_id' => $deviceId],
            [
                'name' => $username,
                'email' => $deviceId . '@guest.moviecat.app',
                'password' => Hash::make(Str::random(24)),
                'role' => 'user',
            ]
        );

        if ($user->name !== $username) {
            $user->name = $username;
            $user->save();
        }

        if ($device && empty($device->username)) {
            $device->update(['username' => $username]);
        }

        $token = $user->createToken('moviecat-guest-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'deviceId' => $deviceId,
            'username' => $username,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'isGuest' => true,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
