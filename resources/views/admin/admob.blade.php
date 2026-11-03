@extends('layouts.app', ['title' => 'AdMob Monetization'])

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white flex items-center gap-2.5">
                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Google AdMob Monetization Center
            </h1>
            <p class="text-xs text-slate-400 mt-1">Global on/off kill-switch, production unit IDs, and live impression telemetry.</p>
        </div>
    </div>

    <!-- Master Switch Card -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border-2 {{ $adsEnabled ? 'border-[#FF2E4C]/40 bg-gradient-to-r from-[#FF2E4C]/10 via-transparent to-transparent' : 'border-slate-700/60 bg-slate-900/50' }} transition-all">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl {{ $adsEnabled ? 'bg-[#FF2E4C] text-white shadow-lg shadow-[#FF2E4C]/30' : 'bg-slate-800 text-slate-400' }} flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">AdMob Master Switch</h2>
                        <div class="flex items-center gap-2 mt-0.5">
                            @if($adsEnabled)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                                    ACTIVE — Ads are showing in the mobile app
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400 mr-1.5"></span>
                                    DISABLED — All mobile ads are silenced
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <p class="text-xs text-slate-400 max-w-xl leading-relaxed">
                    When you turn AdMob <strong>OFF</strong>, the backend transmits <code>ads.enabled = false</code> to all mobile app clients. Banners, interstitial popups, and player banners are silenced immediately without pushing app updates.
                </p>
            </div>

            <form action="{{ route('admin.admob.toggle') }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-4 rounded-2xl font-bold text-sm shadow-xl transition-all flex items-center gap-2.5 hover:scale-105 active:scale-95 {{ $adsEnabled ? 'bg-slate-800 hover:bg-slate-700 text-rose-400 border border-rose-500/30' : 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-emerald-500/25' }}">
                    @if($adsEnabled)
                        <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        <span>Disable AdMob Ads</span>
                    @else
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Enable AdMob Ads</span>
                    @endif
                </button>
            </form>
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