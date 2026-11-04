<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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

        $throttleKey = Str::transliterate(Str::lower($credentials['email']) . '|' . $request->ip());

        // Lock out after 5 consecutive failed attempts for 60 seconds
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            Log::warning('Brute-force login attempt locked out for: ' . $credentials['email'] . ' from IP: ' . $request->ip());
            return back()->withErrors([
                'email' => "Too many failed login attempts. For security, access is temporarily locked for {$seconds} seconds.",
            ])->onlyInput('email');
        }

        try {
            if (Auth::attempt($credentials, (bool)$request->has('remember'))) {
                RateLimiter::clear($throttleKey);
                $request->session()->regenerate();

                if (Auth::user()->role !== 'admin') {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return back()->withErrors(['email' => 'Access denied. Administrator privileges required.']);
                }

                Log::info('Administrator authenticated: ' . Auth::user()->email . ' from IP: ' . $request->ip());
                return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . (Auth::user()->name ?: 'Administrator') . '!');
            }

            RateLimiter::hit($throttleKey, 60);
            Log::warning('Failed admin login attempt for: ' . $credentials['email'] . ' from IP: ' . $request->ip());
        } catch (Throwable $e) {
            Log::error('Admin Auth Exception: ' . $e->getMessage());
            return back()->withErrors([
                'email' => 'Authentication error occurred. Please try again.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password provided.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (Throwable) {}

        return redirect()->route('admin.login')->with('success', 'You have been securely signed out.');
    }
}
