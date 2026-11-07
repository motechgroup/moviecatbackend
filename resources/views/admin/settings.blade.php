@extends('layouts.app', ['title' => 'Remote App Settings'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div>
        <h1 class="text-2xl font-extrabold text-white flex items-center gap-2.5">
            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Remote App Settings & Features
        </h1>
        <p class="text-xs text-slate-400 mt-1">Manage global app announcements, minimum version requirements, and feature flags.</p>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Announcement Banner -->
        <div class="glass-panel p-6 sm:p-7 rounded-3xl space-y-4">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                In-App Announcement Banner
            </h2>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Banner Announcement Text</label>
                <textarea name="announcement_text" rows="2" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#FF2E4C]">{{ $settings['announcement_text'] }}</textarea>
            </div>
            <label class="flex items-center gap-2.5 text-xs text-slate-300 cursor-pointer">
                <input type="checkbox" name="announcement_active" value="1" {{ ($settings['announcement_active'] ?? true) ? 'checked' : '' }} class="w-4 h-4 accent-[#FF2E4C] rounded">
                <span>Display announcement banner in mobile app home screen</span>
            </label>
        </div>

        <!-- Version & Maintenance -->
        <div class="glass-panel p-6 sm:p-7 rounded-3xl space-y-4">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                App Version & System Mode
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Minimum Supported App Version</label>
                    <input type="text" name="min_app_version" value="{{ $settings['min_app_version'] }}" placeholder="1.0.0" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-[#FF2E4C]">
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2.5 text-xs text-slate-300 cursor-pointer pb-2">
                        <input type="checkbox" name="force_update" value="1" {{ ($settings['force_update'] ?? false) ? 'checked' : '' }} class="w-4 h-4 accent-[#FF2E4C] rounded">
                        <span>Force update on outdated versions</span>
                    </label>
                </div>
            </div>

            <div class="pt-3 border-t border-white/5">
                <label class="flex items-center gap-2.5 text-xs text-rose-300 cursor-pointer">
                    <input type="checkbox" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }} class="w-4 h-4 accent-rose-500 rounded">
                    <span>Enable Maintenance Mode (Restricts app access with maintenance notice)</span>
                </label>
            </div>
        </div>

        <!-- Vault Offline Subscription & Payment Gateways -->
        <div class="glass-panel p-6 sm:p-7 rounded-3xl space-y-5 border border-amber-500/20 bg-amber-500/[0.02]">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        Vault Offline Subscription & Payment Gateways
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Online streaming remains 100% free. When enabled, playing downloaded media in the Vault requires an active subscription.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ ($settings['vault_subscription_enabled'] ?? false) ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-slate-700/40 text-slate-400' }}">
                        {{ ($settings['vault_subscription_enabled'] ?? false) ? 'ACTIVE (RESTRICTED)' : 'DISABLED (FREE PLAY)' }}
                    </span>
                </div>
            </div>

            <label class="flex items-center gap-2.5 text-xs text-amber-200 cursor-pointer p-3 rounded-xl bg-amber-500/10 border border-amber-500/20">
                <input type="checkbox" name="vault_subscription_enabled" value="1" {{ ($settings['vault_subscription_enabled'] ?? false) ? 'checked' : '' }} class="w-4 h-4 accent-amber-500 rounded">
                <span class="font-bold text-white">Enable Vault Offline Subscription Requirement (Shows subscription modal when playing downloads in Vault)</span>
            </label>

            <!-- Single Package Configuration -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-2">
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Package Name</label>
                    <input type="text" name="vault_subscription_package_name" value="{{ $settings['vault_subscription_package_name'] ?? 'MovieCat Vault VIP' }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Price</label>
                    <input type="number" step="1" name="vault_subscription_price" value="{{ $settings['vault_subscription_price'] ?? 100 }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 font-mono focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Currency</label>
                    <input type="text" name="vault_subscription_currency" value="{{ $settings['vault_subscription_currency'] ?? 'KES' }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 font-mono uppercase focus:outline-none focus:border-amber-400">
                </div>
            </div>

            <!-- Enabled Payment Gateways -->
            <div class="pt-3 border-t border-white/5 space-y-2">
                <p class="text-xs font-semibold text-slate-200">Active Payment Gateways in Mobile & TV App:</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="flex items-center gap-2.5 p-3 rounded-xl bg-white/[0.02] border border-white/10 text-xs text-slate-200 cursor-pointer">
                        <input type="checkbox" name="gateway_mpesa_enabled" value="1" {{ ($settings['gateway_mpesa_enabled'] ?? true) ? 'checked' : '' }} class="w-4 h-4 accent-emerald-500 rounded">
                        <div>
                            <span class="font-bold text-white block">M-Pesa STK Push</span>
                            <span class="text-[10px] text-slate-400">Direct Safaricom prompt</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2.5 p-3 rounded-xl bg-white/[0.02] border border-white/10 text-xs text-slate-200 cursor-pointer">
                        <input type="checkbox" name="gateway_payhero_enabled" value="1" {{ ($settings['gateway_payhero_enabled'] ?? true) ? 'checked' : '' }} class="w-4 h-4 accent-emerald-500 rounded">
                        <div>
                            <span class="font-bold text-white block">PayHero</span>
                            <span class="text-[10px] text-slate-400">M-Pesa & Mobile Money</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2.5 p-3 rounded-xl bg-white/[0.02] border border-white/10 text-xs text-slate-200 cursor-pointer">
                        <input type="checkbox" name="gateway_flutterwave_enabled" value="1" {{ ($settings['gateway_flutterwave_enabled'] ?? true) ? 'checked' : '' }} class="w-4 h-4 accent-emerald-500 rounded">
                        <div>
                            <span class="font-bold text-white block">Flutterwave</span>
                            <span class="text-[10px] text-slate-400">East Africa (KE, UG, TZ, RW)</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Feature Toggles -->
        <div class="glass-panel p-6 sm:p-7 rounded-3xl space-y-4">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Client Feature Flags
            </h2>
            <div class="space-y-3 text-xs">
                <label class="flex items-center gap-2.5 text-slate-200 cursor-pointer p-2.5 rounded-xl bg-white/[0.02] border border-white/5">
                    <input type="checkbox" name="feature_downloads_enabled" value="1" {{ ($settings['feature_downloads_enabled'] ?? true) ? 'checked' : '' }} class="w-4 h-4 accent-[#FF2E4C] rounded">
                    <span class="font-bold text-white">Enable Offline Downloads (Allows users to download movies and series into device vault)</span>
                </label>
                <label class="flex items-center gap-2.5 text-slate-300 cursor-pointer">
                    <input type="checkbox" name="feature_bulk_download" value="1" {{ ($settings['feature_bulk_download'] ?? true) ? 'checked' : '' }} class="w-4 h-4 accent-[#FF2E4C] rounded">
                    <span>Bulk Downloads for TV series & movies</span>
                </label>
                <label class="flex items-center gap-2.5 text-slate-300 cursor-pointer">
                    <input type="checkbox" name="feature_similar_series" value="1" {{ ($settings['feature_similar_series'] ?? true) ? 'checked' : '' }} class="w-4 h-4 accent-[#FF2E4C] rounded">
                    <span>Similar Series recommendations section</span>
                </label>
                <label class="flex items-center gap-2.5 text-slate-300 cursor-pointer">
                    <input type="checkbox" name="feature_cloud_sync" value="1" {{ ($settings['feature_cloud_sync'] ?? true) ? 'checked' : '' }} class="w-4 h-4 accent-[#FF2E4C] rounded">
                    <span>Cloud Watch Progress Sync</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-7 py-3 rounded-2xl bg-[#FF2E4C] hover:bg-[#E02440] text-white text-xs font-bold shadow-lg shadow-[#FF2E4C]/30 transition-all">
                Save Remote App Settings
            </button>
        </div>
    </form>
</div>
@endsection