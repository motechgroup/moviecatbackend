@extends('layouts.app', ['title' => 'Administrator Profile & Security'])

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- HERO HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 sm:p-8 rounded-3xl glass-panel relative overflow-hidden border border-white/10 shadow-2xl">
        <div class="space-y-1 relative z-10">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#FF2E4C]/15 text-[#FF2E4C] border border-[#FF2E4C]/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF2E4C] animate-pulse"></span>
                    Root Administrator
                </span>
                <span class="text-xs text-slate-400 font-mono">• Security & Access Portal</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-2.5">
                <span>Account Profile & Security</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 max-w-xl">
                Manage your administrative credentials, update your password, and monitor active session telemetry.
            </p>
        </div>

        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-bold transition-all border border-white/10 flex items-center gap-2">
                <span>&larr; Back to Dashboard</span>
            </a>
        </div>
    </div>

    <!-- NOTIFICATIONS -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs flex items-center gap-3 shadow-lg shadow-emerald-500/10">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs space-y-1 shadow-lg shadow-rose-500/10">
            <div class="font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Please correct the errors below:</span>
            </div>
            @foreach($errors->all() as $error)
                <div class="pl-6">&bull; {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- LEFT COLUMN: PROFILE CARD & SESSION TELEMETRY -->
        <div class="space-y-6">
            <!-- Administrator Identity Badge -->
            <div class="p-6 rounded-3xl glass-panel border border-white/10 shadow-2xl text-center space-y-4">
                <div class="relative inline-block mx-auto">
                    <img src="{{ asset('images/icon.png') }}" alt="MovieCat Official Logo" class="w-20 h-20 rounded-3xl mx-auto shadow-2xl shadow-[#FF2E4C]/30 object-cover border-2 border-[#FF2E4C]/40" />
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-[#0A0B10] flex items-center justify-center text-[10px] text-white font-bold" title="Active">✓</span>
                </div>

                <div>
                    <h2 class="text-lg font-black text-white tracking-tight">{{ $user->name }}</h2>
                    <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $user->email }}</p>
                </div>

                <div class="pt-3 border-t border-white/5 flex items-center justify-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-400 border border-rose-500/30">
                        {{ strtoupper($user->role ?? 'ADMIN') }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-mono text-slate-400 bg-white/5">
                        ID: #{{ $user->id }}
                    </span>
                </div>

                <div class="text-[11px] text-slate-500 space-y-1 pt-2">
                    <div>Member since: <span class="text-slate-300 font-mono">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'Genesis' }}</span></div>
                    <div>Account Status: <span class="text-emerald-400 font-bold">Active & Verified</span></div>
                </div>
            </div>

            <!-- Session & Security Info -->
            <div class="p-6 rounded-3xl glass-panel border border-white/10 shadow-2xl space-y-3 text-xs">
                <div class="font-bold text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Active Session Security</span>
                </div>
                <div class="space-y-2 text-slate-400 font-mono text-[11px]">
                    <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                        <span>Client IP:</span>
                        <span class="text-slate-200">{{ $sessionInfo['ip'] }}</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                        <span>Auth Guard:</span>
                        <span class="text-emerald-400">Web Session (CSRF)</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                        <span>Brute-force:</span>
                        <span class="text-rose-400">Rate Limiter Active</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: UPDATE PROFILE & UPDATE PASSWORD FORMS -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- 1. General Profile Info Form -->
            <div class="p-6 sm:p-7 rounded-3xl glass-panel border border-white/10 shadow-2xl space-y-5">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Administrator Profile Details</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Update your display name and login email address.</p>
                </div>

                <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Administrator Name</label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name', $user->name) }}" 
                               required 
                               placeholder="e.g. MovieCat Admin"
                               class="w-full bg-[#12131A] border border-white/10 rounded-xl px-4 py-3 text-xs sm:text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-[#FF2E4C] transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Address</label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}" 
                               required 
                               placeholder="admin@moviecat.app"
                               class="w-full bg-[#12131A] border border-white/10 rounded-xl px-4 py-3 text-xs sm:text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-[#FF2E4C] transition-colors">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-600 text-white text-xs font-bold shadow-lg shadow-rose-600/30 transition-all flex items-center gap-2">
                            <span>Save Profile Changes</span>
                            <span class="text-xs">&rarr;</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- 2. Security & Password Update Form -->
            <div class="p-6 sm:p-7 rounded-3xl glass-panel border border-white/10 shadow-2xl space-y-5">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Change Administrator Password</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Ensure your account is protected with a long, unique passphrase (at least 8 characters).</p>
                </div>

                <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Current Password</label>
                        <input type="password" 
                               name="current_password" 
                               required 
                               placeholder="Enter your current password"
                               class="w-full bg-[#12131A] border border-white/10 rounded-xl px-4 py-3 text-xs sm:text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-purple-500 transition-colors">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">New Password</label>
                            <input type="password" 
                                   name="password" 
                                   required 
                                   placeholder="Minimum 8 characters"
                                   class="w-full bg-[#12131A] border border-white/10 rounded-xl px-4 py-3 text-xs sm:text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-purple-500 transition-colors">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Confirm New Password</label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   required 
                                   placeholder="Re-enter new password"
                                   class="w-full bg-[#12131A] border border-white/10 rounded-xl px-4 py-3 text-xs sm:text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-purple-500 transition-colors">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-xs font-bold shadow-lg shadow-purple-600/30 transition-all flex items-center gap-2">
                            <span>Update Password</span>
                            <span class="text-xs">&rarr;</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection
