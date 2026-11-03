<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        try {
            if (Auth::check() && Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
        } catch (Throwable) {}

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        Log::info('Login attempt for: ' . $credentials['email']);

        try {
            if (Auth::attempt($credentials, (bool)$request->has('remember'))) {
                $request->session()->regenerate();
                Log::info('Auth::attempt succeeded. User role: ' . Auth::user()->role);

                if (Auth::user()->role !== 'admin') {
                    Auth::logout();
                    return back()->withErrors(['email' => 'Access denied. Administrator privileges required.']);
                }

                return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Administrator!');
            }
            Log::warning('Auth::attempt failed for: ' . $credentials['email']);
        } catch (Throwable $e) {
            Log::error('Auth error: ' . $e->getMessage());
            return back()->withErrors([
                'email' => 'Database error: ' . $e->getMessage(),
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (Throwable) {}

        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }
}