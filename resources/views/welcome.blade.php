@extends('layouts.app', ['title' => 'MovieCat API - High Speed Streaming Backend'])

@section('content')
<div class="relative overflow-hidden pt-12 pb-20">
    <!-- Background Glow -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-[#FF2E4C]/15 to-transparent blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <!-- Hero Section -->
        <div class="text-center max-w-3xl mx-auto space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-medium text-slate-300">
                <span class="w-2 h-2 rounded-full bg-[#FF2E4C]"></span>
                Connected Mobile App Backend
            </div>
            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight leading-tight">
                Backend Services for <span class="bg-gradient-to-r from-[#FF2E4C] to-[#FF8596] bg-clip-text text-transparent">MovieCat</span>
            </h1>
            <p class="text-lg text-slate-400 leading-relaxed">
                Production-ready RESTful API powering mobile movie & TV streaming, AdMob remote configuration, cross-device watch progress sync, bookmarks, and catalog management.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 rounded-xl bg-[#FF2E4C] hover:bg-[#E02440] text-white font-semibold text-sm shadow-lg shadow-[#FF2E4C]/30 transition-all hover:scale-105">
                    Open Admin Dashboard &rarr;
                </a>
                <a href="/api/v1/config" target="_blank" class="px-6 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 font-semibold text-sm transition-colors">
                    View Live API Config JSON
                </a>
            </div>
        </div>

        <!-- Tech Stack Pill Row -->
        <div class="mt-16 grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-4xl mx-auto">
            <div class="glass-panel p-5 rounded-2xl text-center">
                <div class="text-2xl font-bold text-white">Laravel 12/13</div>
                <div class="text-xs text-slate-400 mt-1">High-performance REST API</div>
            </div>
            <div class="glass-panel p-5 rounded-2xl text-center">
                <div class="text-2xl font-bold text-white">MySQL</div>
                <div class="text-xs text-slate-400 mt-1">Relational Database Engine</div>
            </div>
            <div class="glass-panel p-5 rounded-2xl text-center">
                <div class="text-2xl font-bold text-white">Tailwind CSS</div>
                <div class="text-xs text-slate-400 mt-1">Sleek Dark Theme Admin UI</div>
            </div>
            <div class="glass-panel p-5 rounded-2xl text-center">
                <div class="text-2xl font-bold text-white">Sanctum Auth</div>
                <div class="text-xs text-slate-400 mt-1">Mobile Token & Guest Security</div>
            </div>
        </div>

        <!-- API Endpoints Matrix -->
        <div class="mt-20">
            <h2 class="text-2xl font-bold tracking-tight text-white mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-[#FF2E4C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Core Mobile API Endpoints
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="glass-panel p-5 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">GET</span>
                        <code class="text-xs text-slate-400">/api/v1/config</code>
                    </div>
                    <p class="text-sm text-slate-300">Remote app config, dynamic AdMob unit IDs (interstitial, rewarded, banner), feature flags, and announcement banners.</p>
                </div>

                <div class="glass-panel p-5 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold px-2 py-1 rounded bg-blue-500/20 text-blue-400 border border-blue-500/30">POST</span>
                        <code class="text-xs text-slate-400">/api/v1/sync/progress</code>
                    </div>
                    <p class="text-sm text-slate-300">Cloud synchronization for TV series & movie playback positions (resumes exactly where user left off on any device).</p>
                </div>

                <div class="glass-panel p-5 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">GET</span>
                        <code class="text-xs text-slate-400">/api/v1/sync/progress</code>
                    </div>
                    <p class="text-sm text-slate-300">Fetch continuous watch history dictionary keyed by movie/episode ID.</p>
                </div>

                <div class="glass-panel p-5 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold px-2 py-1 rounded bg-purple-500/20 text-purple-400 border border-purple-500/30">POST</span>
                        <code class="text-xs text-slate-400">/api/v1/auth/guest-token</code>
                    </div>
                    <p class="text-sm text-slate-300">Seamless instant device token issuance using device ID for frictionless zero-login user experience.</p>
                </div>

                <div class="glass-panel p-5 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">GET</span>
                        <code class="text-xs text-slate-400">/api/v1/featured</code>
                    </div>
                    <p class="text-sm text-slate-300">Curated featured carousels and top banner spotlight items for the app home screen.</p>
                </div>

                <div class="glass-panel p-5 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold px-2 py-1 rounded bg-amber-500/20 text-amber-400 border border-amber-500/30">POST</span>
                        <code class="text-xs text-slate-400">/api/v1/telemetry/ad-event</code>
                    </div>
                    <p class="text-sm text-slate-300">Live AdMob impression, click, and fill rate telemetry reporting from physical devices.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection