@extends('layouts.app', ['title' => 'Administrator Profile & Security'])

@section('content')
<style>
    .profile-bento-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        align-items: start;
    }
    @media (min-width: 1024px) {
        .profile-bento-grid {
            grid-template-columns: 360px 1fr;
            gap: 1.75rem;
        }
    }
    .profile-input-group {
        position: relative;
    }
    .profile-input-icon {
        position: absolute;
        top: 50%;
        left: 0.875rem;
        transform: translateY(-50%);
        pointer-events: none;
        color: #94a3b8;
    }
    .profile-input {
        width: 100%;
        background-color: #0E1017;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.75rem;
        padding: 0.75rem 1rem 0.75rem 2.6rem;
        font-size: 0.875rem;
        color: #f8fafc;
        transition: all 0.2s ease-in-out;
    }
    .profile-input:focus {
        outline: none;
        border-color: #FF2E4C;
        box-shadow: 0 0 0 1px #FF2E4C;
    }
    .profile-input-purple:focus {
        outline: none;
        border-color: #a855f7;
        box-shadow: 0 0 0 1px #a855f7;
    }
</style>

<div class="max-w-7xl mx-auto space-y-6 pb-12">

    <!-- 1. TOP HERO BANNER -->
    <div class="relative overflow-hidden rounded-3xl glass-panel border border-white/10 p-6 sm:p-8 shadow-2xl bg-gradient-to-br from-[#161822] via-[#12131C] to-[#0C0E14]">
        <!-- Ambient Background Glows -->
        <div class="absolute -top-20 -right-20 w-72 h-72 bg-[#FF2E4C]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-[#FF2E4C]/15 text-[#FF2E4C] border border-[#FF2E4C]/30 shadow-sm shadow-[#FF2E4C]/20">
                        <span class="w-2 h-2 rounded-full bg-[#FF2E4C] animate-pulse"></span>
                        ROOT PRIVILEGE ACTIVE
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        TLS / HTTPS Encrypted
                    </span>
                    <span class="text-xs text-slate-500 font-mono hidden sm:inline">• MovieCat Security Hub</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                    <span>Administrator Profile & Security</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 max-w-2xl leading-relaxed">
                    Review your master administrator credentials, rotate your system authentication passphrase, and monitor active security telemetry.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white text-xs font-bold transition-all border border-white/10 flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="/api/v1/config" target="_blank" class="px-4 py-2.5 rounded-xl bg-[#FF2E4C]/10 hover:bg-[#FF2E4C]/20 text-[#FF2E4C] text-xs font-bold transition-all border border-[#FF2E4C]/30 flex items-center gap-2 shadow-sm shadow-[#FF2E4C]/10">
                    <span>API Config</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- NOTIFICATIONS & ALERTS -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-lg shadow-emerald-500/10">
            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="p-4 sm:p-5 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs sm:text-sm space-y-2 shadow-lg shadow-rose-500/10">
            <div class="font-bold flex items-center gap-2 text-rose-200">
                <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Action Required: Please correct the following issues:</span>
            </div>
            <ul class="pl-7 space-y-1 list-disc text-rose-300/90 text-xs font-mono">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 2. BENTO GRID LAYOUT -->
    <div class="profile-bento-grid">

        <!-- LEFT COLUMN: IDENTITY & TELEMETRY -->
        <div class="space-y-6">

            <!-- Administrator Identity Master Card -->
            <div class="rounded-3xl glass-panel border border-white/10 p-6 sm:p-7 shadow-2xl relative overflow-hidden text-center space-y-5 bg-[#141620]">
                <div class="absolute top-0 left-0 right-0 h-28 bg-gradient-to-b from-[#FF2E4C]/15 to-transparent pointer-events-none"></div>

                <!-- Avatar with glowing border -->
                <div class="relative inline-block mx-auto mt-2">
                    <div class="w-24 h-24 rounded-3xl p-1 bg-gradient-to-tr from-[#FF2E4C] via-[#FF6B6B] to-purple-500 shadow-xl shadow-[#FF2E4C]/30">
                        <img src="{{ asset('images/icon.png') }}" alt="MovieCat Official Avatar" class="w-full h-full rounded-[22px] object-cover bg-[#0A0B10]" />
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 border-2 border-[#141620] flex items-center justify-center text-white text-[11px] font-bold shadow-md shadow-emerald-500/40" title="Active Master Session">
                        ✓
                    </span>
                </div>

                <div class="space-y-1">
                    <h2 class="text-xl font-black text-white tracking-tight">{{ $user->name }}</h2>
                    <p class="text-xs text-slate-400 font-mono select-all bg-black/40 py-1 px-3 rounded-lg border border-white/5 inline-block">{{ $user->email }}</p>
                </div>

                <div class="flex items-center justify-center gap-2 pt-1">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-400 border border-rose-500/30">
                        {{ strtoupper($user->role ?? 'MASTER ADMIN') }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-[10px] font-mono font-bold text-slate-300 bg-white/5 border border-white/10">
                        UID #{{ $user->id }}
                    </span>
                </div>

                <!-- Identity Metadata Rows -->
                <div class="pt-4 border-t border-white/5 text-xs text-left space-y-2.5">
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/[0.02] border border-white/5">
                        <span class="text-slate-400">Account Status</span>
                        <span class="font-bold text-emerald-400 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            Active & Verified
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/[0.02] border border-white/5">
                        <span class="text-slate-400">Access Scope</span>
                        <span class="font-mono text-slate-200">Full Root Console</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/[0.02] border border-white/5">
                        <span class="text-slate-400">Enrolled On</span>
                        <span class="font-mono text-slate-200">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'Genesis' }}</span>
                    </div>
                </div>
            </div>

            <!-- Active Session Security Card -->
            <div class="rounded-3xl glass-panel border border-white/10 p-6 shadow-2xl space-y-4 bg-[#141620]">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-sky-500/15 flex items-center justify-center text-sky-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white">Active Session Security</h3>
                            <p class="text-[11px] text-slate-400">Client handshake & telemetry</p>
                        </div>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                </div>

                <div class="space-y-2 text-xs font-mono">
                    <div class="p-3 rounded-xl bg-black/40 border border-white/5 flex items-center justify-between">
                        <span class="text-slate-400">Client IP:</span>
                        <span class="text-white font-bold">{{ $sessionInfo['ip'] ?? request()->ip() }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-black/40 border border-white/5 flex items-center justify-between">
                        <span class="text-slate-400">Auth Guard:</span>
                        <span class="text-emerald-400 font-bold">Web Session (CSRF)</span>
                    </div>
                    <div class="p-3 rounded-xl bg-black/40 border border-white/5 flex items-center justify-between">
                        <span class="text-slate-400">Rate Limiter:</span>
                        <span class="text-sky-400 font-bold">60 req/min Active</span>
                    </div>
                </div>

                <div class="pt-2">
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-white/5 hover:bg-rose-500/15 text-slate-300 hover:text-rose-300 border border-white/10 hover:border-rose-500/30 text-xs font-bold transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Sign Out Master Session</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: PROFILE & PASSWORD EDIT FORMS -->
        <div class="space-y-6">

            <!-- 1. GENERAL PROFILE DETAILS FORM -->
            <div class="rounded-3xl glass-panel border border-white/10 p-6 sm:p-8 shadow-2xl space-y-6 bg-[#141620]">
                <div class="flex items-center justify-between pb-4 border-b border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#FF2E4C]/15 text-[#FF2E4C] flex items-center justify-center border border-[#FF2E4C]/30 shadow-md shadow-[#FF2E4C]/10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-white tracking-tight">Administrator Profile Details</h2>
                            <p class="text-xs text-slate-400">Update your console display name and official login email address.</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-300">
                                Administrator Display Name <span class="text-rose-500">*</span>
                            </label>
                            <div class="profile-input-group">
                                <svg class="w-4 h-4 profile-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <input type="text" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required 
                                       placeholder="e.g. MovieCat Admin"
                                       class="profile-input">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-300">
                                Administrative Email Address <span class="text-rose-500">*</span>
                            </label>
                            <div class="profile-input-group">
                                <svg class="w-4 h-4 profile-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                                <input type="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required 
                                       placeholder="admin@moviecat.app"
                                       class="profile-input font-mono">
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <span class="text-[11px] text-slate-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Changes propagate immediately across the console session.
                        </span>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#FF2E4C] to-[#E02440] hover:from-[#E02440] hover:to-[#FF2E4C] text-white text-xs font-bold shadow-lg shadow-[#FF2E4C]/30 hover:shadow-[#FF2E4C]/50 transition-all flex items-center justify-center gap-2">
                            <span>Save Profile Changes</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- 2. PASSWORD ROTATION FORM -->
            <div class="rounded-3xl glass-panel border border-white/10 p-6 sm:p-8 shadow-2xl space-y-6 bg-[#141620]">
                <div class="flex items-center justify-between pb-4 border-b border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-purple-500/15 text-purple-400 flex items-center justify-center border border-purple-500/30 shadow-md shadow-purple-500/10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-white tracking-tight">Rotate Master Password</h2>
                            <p class="text-xs text-slate-400">Ensure your administrative access remains shielded with a strong cryptographic password.</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300">
                            Current Master Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="profile-input-group">
                            <svg class="w-4 h-4 profile-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            <input type="password" 
                                   name="current_password" 
                                   required 
                                   placeholder="Enter your existing administrator password"
                                   class="profile-input profile-input-purple font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-300">
                                New Passphrase <span class="text-rose-500">*</span>
                            </label>
                            <div class="profile-input-group">
                                <svg class="w-4 h-4 profile-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <input type="password" 
                                       name="password" 
                                       required 
                                       placeholder="Minimum 8 characters"
                                       class="profile-input profile-input-purple font-mono">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-300">
                                Confirm New Passphrase <span class="text-rose-500">*</span>
                            </label>
                            <div class="profile-input-group">
                                <svg class="w-4 h-4 profile-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <input type="password" 
                                       name="password_confirmation" 
                                       required 
                                       placeholder="Re-enter new passphrase"
                                       class="profile-input profile-input-purple font-mono">
                            </div>
                        </div>
                    </div>

                    <!-- Security Tips Checklist -->
                    <div class="p-4 rounded-2xl bg-black/40 border border-white/5 space-y-2 text-xs text-slate-400">
                        <div class="font-bold text-slate-300 flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Password Security Recommendations</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[11px] pt-1">
                            <span class="flex items-center gap-1.5 text-slate-400">
                                <span class="text-emerald-400 font-bold">&bull;</span> Min 8 characters
                            </span>
                            <span class="flex items-center gap-1.5 text-slate-400">
                                <span class="text-emerald-400 font-bold">&bull;</span> Mix letters & numbers
                            </span>
                            <span class="flex items-center gap-1.5 text-slate-400">
                                <span class="text-emerald-400 font-bold">&bull;</span> Salted bcrypt hash
                            </span>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-end">
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-xs font-bold shadow-lg shadow-purple-600/30 hover:shadow-purple-600/50 transition-all flex items-center justify-center gap-2">
                            <span>Update Administrator Password</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
