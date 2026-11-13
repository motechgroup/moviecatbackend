@extends('layouts.app', ['title' => 'AdMob & MovieCat Monetization'])

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white flex items-center gap-2.5">
                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                AdMob & MovieCat Ads Control Center
            </h1>
            <p class="text-xs text-slate-400 mt-1">Independent controls for Google AdMob and custom MovieCat sponsor ads pushed in real time.</p>
        </div>
    </div>

    <!-- Switches Grid: AdMob and MovieCat Ads -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- 1. AdMob Master Switch Card -->
        <div class="glass-panel p-6 rounded-3xl border-2 {{ $adsEnabled ? 'border-[#FF2E4C]/40 bg-gradient-to-r from-[#FF2E4C]/10 via-transparent to-transparent' : 'border-slate-700/60 bg-slate-900/50' }} flex flex-col justify-between space-y-5">
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl {{ $adsEnabled ? 'bg-[#FF2E4C] text-white shadow-lg shadow-[#FF2E4C]/30' : 'bg-slate-800 text-slate-400' }} flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">Google AdMob Ads</h2>
                        <div class="flex items-center gap-2 mt-0.5">
                            @if($adsEnabled)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                                    ACTIVE — Showing Google Ads
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400 mr-1.5"></span>
                                    DISABLED — Google Ads Silenced
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Controls Google AdMob banners and interstitial ads across the mobile app.
                </p>
            </div>

            <form action="{{ route('admin.admob.toggle') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-xs shadow-lg transition-all flex items-center justify-center gap-2 {{ $adsEnabled ? 'bg-slate-800 hover:bg-slate-700 text-rose-400 border border-rose-500/30' : 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-emerald-500/25' }}">
                    @if($adsEnabled)
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        <span>Disable Google AdMob</span>
                    @else
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Enable Google AdMob</span>
                    @endif
                </button>
            </form>
        </div>

        <!-- 2. MovieCat Ads Master Switch Card -->
        <div class="glass-panel p-6 rounded-3xl border-2 {{ $movieCatAdsEnabled ? 'border-amber-500/40 bg-gradient-to-r from-amber-500/10 via-transparent to-transparent' : 'border-slate-700/60 bg-slate-900/50' }} flex flex-col justify-between space-y-5">
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl {{ $movieCatAdsEnabled ? 'bg-amber-500 text-black shadow-lg shadow-amber-500/30' : 'bg-slate-800 text-slate-400' }} flex items-center justify-center font-black">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">MovieCat Ads Switch</h2>
                        <div class="flex items-center gap-2 mt-0.5">
                            @if($movieCatAdsEnabled)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5 animate-pulse"></span>
                                    ACTIVE — Showing In-House Ads
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-700/50 text-slate-400 border border-slate-600/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span>
                                    DISABLED — In-House Ads Silenced
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Controls in-house sponsor and promo banners independently. Turning this OFF silences all MovieCat ads immediately.
                </p>
            </div>

            <form action="{{ route('admin.admob.toggle-moviecat-ads') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-xs shadow-lg transition-all flex items-center justify-center gap-2 {{ $movieCatAdsEnabled ? 'bg-slate-800 hover:bg-slate-700 text-rose-400 border border-rose-500/30' : 'bg-gradient-to-r from-amber-500 to-yellow-500 text-black font-extrabold shadow-amber-500/25' }}">
                    @if($movieCatAdsEnabled)
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        <span>Disable MovieCat Ads</span>
                    @else
                        <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Enable MovieCat Ads</span>
                    @endif
                </button>
            </form>
        </div>
    </div>

    <!-- Create & Push Custom MovieCat Ad Form -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create & Push Custom MovieCat Ad
                </h2>
                <p class="text-xs text-slate-400 mt-1">Configure creative details below to push new in-house banners directly to the mobile app.</p>
            </div>
        </div>

        <form action="{{ route('admin.admob.moviecat-ads.create') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Brand Name *</label>
                    <input type="text" name="brandName" placeholder="e.g. MovieCat VIP Pass" required class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Badge Tag</label>
                    <input type="text" name="badge" placeholder="e.g. OFFICIAL CLUB, 50% OFF" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Accent Color</label>
                    <select name="accentColor" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400">
                        <option value="#FFB300">Gold / VIP (#FFB300)</option>
                        <option value="#FF4B55">Cinema Red (#FF4B55)</option>
                        <option value="#3B82F6">Blue (#3B82F6)</option>
                        <option value="#10B981">Green (#10B981)</option>
                        <option value="#EC4899">Pink (#EC4899)</option>
                        <option value="#8B5CF6">Purple (#8B5CF6)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Headline Title *</label>
                    <input type="text" name="title" placeholder="e.g. Ultra 4K HDR & Offline Vault" required class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Call To Action (CTA Text) *</label>
                    <input type="text" name="ctaText" placeholder="e.g. Get VIP Access" required class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Tagline / Description *</label>
                <input type="text" name="tagline" placeholder="e.g. Unlimited high-speed downloads across devices & priority media streaming servers." required class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Destination Target URL *</label>
                    <input type="url" name="url" placeholder="https://moviecat.top or https://..." required class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Banner Image URL (Optional)</label>
                    <input type="url" name="imageUrl" placeholder="https://example.com/banner.jpg" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-xs bg-amber-500 hover:bg-amber-400 text-black transition-all flex items-center gap-2 shadow-lg shadow-amber-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span>Push Ad to Mobile App</span>
                </button>
            </div>
        </form>

        <!-- Active Pushed MovieCat Ads List -->
        <div class="pt-6 border-t border-white/10">
            <h3 class="text-xs font-bold uppercase text-slate-400 mb-3">Live Pushed MovieCat Ads ({{ count($customAds) }})</h3>
            @if(empty($customAds))
                <div class="bg-[#12131A] border border-white/5 p-4 rounded-2xl text-center text-xs text-slate-500">
                    No custom ads created yet. The mobile app will display the default verified sponsors (StreamShield VPN, Dolby Atmos, MovieCat VIP, OLED 4K, Cinema Gourmet) unless disabled.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($customAds as $ad)
                        <div class="bg-[#12131A] border border-white/10 p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-base" style="background-color: {{ ($ad['accentColor'] ?? '#FFB300') }}20; border: 1px solid {{ ($ad['accentColor'] ?? '#FFB300') }}60; color: {{ ($ad['accentColor'] ?? '#FFB300') }};">
                                    ★
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-white">{{ $ad['title'] }}</span>
                                        <span class="text-[10px] px-2 py-0.5 rounded font-extrabold" style="background-color: {{ ($ad['accentColor'] ?? '#FFB300') }}25; color: {{ ($ad['accentColor'] ?? '#FFB300') }};">
                                            {{ $ad['badge'] ?? 'PROMO' }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-400">{{ $ad['brandName'] }} • {{ $ad['tagline'] }}</div>
                                    <div class="text-[10px] text-slate-500 truncate max-w-sm">{{ $ad['url'] }}</div>
                                </div>
                            </div>

                            <form action="{{ route('admin.admob.moviecat-ads.delete', $ad['id']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Remove this ad from the mobile app?')" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 transition-all">
                                    Remove
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Ad Telemetry Stats -->
    <div class="grid grid-cols-3 gap-4">
        <div class="glass-panel p-5 rounded-2xl text-center">
            <div class="text-xs font-semibold uppercase text-slate-400">Total Impressions</div>
            <div class="text-3xl font-extrabold text-white mt-1">{{ $adStats['impressions'] }}</div>
        </div>
        <div class="glass-panel p-5 rounded-2xl text-center">
            <div class="text-xs font-semibold uppercase text-slate-400">Ad Clicks</div>
            <div class="text-3xl font-extrabold text-emerald-400 mt-1">{{ $adStats['clicks'] }}</div>
        </div>
        <div class="glass-panel p-5 rounded-2xl text-center">
            <div class="text-xs font-semibold uppercase text-slate-400">Fill Failures</div>
            <div class="text-3xl font-extrabold text-slate-400 mt-1">{{ $adStats['failed'] }}</div>
        </div>
    </div>

    <!-- Unit IDs Form -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl space-y-6">
        <h2 class="text-lg font-bold text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            AdMob Production Unit Configuration
        </h2>

        <form action="{{ route('admin.admob.settings') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Android Banner Unit ID</label>
                    <input type="text" name="admob_android_banner_id" value="{{ $settings['admob_android_banner_id'] }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-[#FF2E4C]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Android Interstitial Unit ID</label>
                    <input type="text" name="admob_android_interstitial_id" value="{{ $settings['admob_android_interstitial_id'] }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-[#FF2E4C]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">iOS Banner Unit ID</label>
                    <input type="text" name="admob_ios_banner_id" value="{{ $settings['admob_ios_banner_id'] }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-[#FF2E4C]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">iOS Interstitial Unit ID</label>
                    <input type="text" name="admob_ios_interstitial_id" value="{{ $settings['admob_ios_interstitial_id'] }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-[#FF2E4C]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Interstitial Trigger Frequency (user actions count)</label>
                    <input type="number" name="interstitial_frequency" min="1" max="20" value="{{ $settings['interstitial_frequency'] }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#FF2E4C]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Banner Rotation Interval (seconds)</label>
                    <input type="number" name="banner_interval" min="10" max="120" value="{{ $settings['banner_interval'] }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#FF2E4C]">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#FF2E4C] hover:bg-[#E02440] text-white text-xs font-bold shadow-md shadow-[#FF2E4C]/25 transition-all">
                    Save AdMob Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
