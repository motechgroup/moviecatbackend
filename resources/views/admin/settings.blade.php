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

            <!-- Subscription Package & Period Plans (1 Week & 1 Month) -->
            <div class="space-y-3 pt-2">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">Package Display Name</label>
                        <input type="text" name="vault_subscription_package_name" value="{{ $settings['vault_subscription_package_name'] ?? 'MovieCat Vault VIP' }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-amber-400">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">Currency</label>
                        <input type="text" name="vault_subscription_currency" value="{{ $settings['vault_subscription_currency'] ?? 'KES' }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 font-mono uppercase focus:outline-none focus:border-amber-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <!-- 1 WEEK PLAN -->
                    <div class="p-3.5 rounded-xl bg-white/[0.02] border border-white/10 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-xs text-slate-200 cursor-pointer font-bold">
                                <input type="checkbox" name="vault_sub_week_enabled" value="1" {{ ($settings['vault_sub_week_enabled'] ?? true) ? 'checked' : '' }} class="w-4 h-4 accent-amber-500 rounded">
                                <span class="text-white text-xs">1 Week Plan (7 Days)</span>
                            </label>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20">7 Days</span>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-400 mb-1">Price (KES)</label>
                            <input type="number" step="1" name="vault_sub_price_week" value="{{ $settings['vault_sub_price_week'] ?? 50 }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-amber-400">
                        </div>
                    </div>

                    <!-- 1 MONTH PLAN -->
                    <div class="p-3.5 rounded-xl bg-white/[0.02] border border-white/10 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-xs text-slate-200 cursor-pointer font-bold">
                                <input type="checkbox" name="vault_sub_month_enabled" value="1" {{ ($settings['vault_sub_month_enabled'] ?? true) ? 'checked' : '' }} class="w-4 h-4 accent-amber-500 rounded">
                                <span class="text-white text-xs">1 Month Plan (30 Days)</span>
                            </label>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">Best Value • 30 Days</span>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-400 mb-1">Price (KES)</label>
                            <input type="number" step="1" name="vault_sub_price_month" value="{{ $settings['vault_sub_price_month'] ?? 100 }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-amber-400">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Gateways Management (Enable/Disable & Credentials) -->
            <div class="pt-5 border-t border-white/10 space-y-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            Payment Gateways Management
                        </h3>
                        <p class="text-[11px] text-slate-400">Manage credentials and activate/deactivate payment options shown to users.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- 1. M-PESA DARAJA STK PUSH -->
                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/10 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2.5 text-xs text-slate-200 cursor-pointer font-bold">
                                <input type="checkbox" name="gateway_mpesa_enabled" value="1" {{ ($settings['gateway_mpesa_enabled'] ?? true) ? 'checked' : '' }} class="w-4 h-4 accent-emerald-500 rounded">
                                <span class="text-white text-sm">Safaricom M-Pesa STK Push</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-semibold border border-emerald-500/30">Direct PIN Prompt</span>
                            </label>
                            <label class="flex items-center gap-2 text-[11px] text-slate-300 cursor-pointer">
                                <input type="checkbox" name="mpesa_live" value="1" {{ ($settings['mpesa_live'] ?? false) ? 'checked' : '' }} class="w-3.5 h-3.5 accent-amber-500 rounded">
                                <span>Live Production Mode (Uncheck for Sandbox)</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">Shortcode (Paybill/Till)</label>
                                <input type="text" name="mpesa_shortcode" value="{{ $settings['mpesa_shortcode'] ?? '174379' }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-amber-400">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">Passkey</label>
                                <input type="text" name="mpesa_passkey" value="{{ $settings['mpesa_passkey'] ?? 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919' }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-amber-400">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">Consumer Key</label>
                                <input type="text" name="mpesa_consumer_key" value="{{ $settings['mpesa_consumer_key'] ?? '' }}" placeholder="e.g. 9a7lKfNJ..." class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-amber-400">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">Consumer Secret</label>
                                <input type="text" name="mpesa_consumer_secret" value="{{ $settings['mpesa_consumer_secret'] ?? '' }}" placeholder="Consumer Secret" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-amber-400">
                            </div>
                            <div class="sm:col-span-2 lg:col-span-4">
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">M-Pesa Webhook Callback URL</label>
                                <input type="text" name="mpesa_callback_url" value="{{ $settings['mpesa_callback_url'] ?? 'https://lindrapp.top/api/v1/webhooks/mpesa' }}" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-amber-400">
                            </div>
                        </div>
                    </div>

                    <!-- 2. PAYHERO MOBILE MONEY -->
                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/10 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2.5 text-xs text-slate-200 cursor-pointer font-bold">
                                <input type="checkbox" name="gateway_payhero_enabled" value="1" {{ ($settings['gateway_payhero_enabled'] ?? true) ? 'checked' : '' }} class="w-4 h-4 accent-emerald-500 rounded">
                                <span class="text-white text-sm">PayHero Gateway</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 font-semibold border border-cyan-500/30">Fast M-Pesa & Mobile Money</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">API Username</label>
                                <input type="text" name="payhero_api_username" value="{{ $settings['payhero_api_username'] ?? '' }}" placeholder="API Username" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-amber-400">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">API Password</label>
                                <input type="text" name="payhero_api_password" value="{{ $settings['payhero_api_password'] ?? '' }}" placeholder="API Password" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-amber-400">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">Basic Auth Token</label>
                                <input type="text" name="payhero_basic_token" value="{{ $settings['payhero_basic_token'] ?? '' }}" placeholder="Basic ..." class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-amber-400">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">Channel ID (Optional)</label>
                                <input type="text" name="payhero_channel_id" value="{{ $settings['payhero_channel_id'] ?? '14226' }}" placeholder="Auto / Default" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-amber-400">
                            </div>
                        </div>
                    </div>

                    <!-- 3. FLUTTERWAVE (EAST AFRICA) -->
                    <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/10 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2.5 text-xs text-slate-200 cursor-pointer font-bold">
                                <input type="checkbox" name="gateway_flutterwave_enabled" value="1" {{ ($settings['gateway_flutterwave_enabled'] ?? true) ? 'checked' : '' }} class="w-4 h-4 accent-emerald-500 rounded">
                                <span class="text-white text-sm">Flutterwave (East Africa)</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 font-semibold border border-indigo-500/30">Kenya, Uganda, Tanzania, Rwanda</span>
                            </label>
                            <label class="flex items-center gap-2 text-[11px] text-slate-300 cursor-pointer">
                                <input type="checkbox" name="flutterwave_live" value="1" {{ ($settings['flutterwave_live'] ?? false) ? 'checked' : '' }} class="w-3.5 h-3.5 accent-amber-500 rounded">
                                <span>Live Production Mode (Uncheck for Test Mode)</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">Public Key</label>
                                <input type="text" name="flutterwave_public_key" value="{{ $settings['flutterwave_public_key'] ?? '' }}" placeholder="FLWPUBK_TEST-..." class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-amber-400">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">Secret Key</label>
                                <input type="text" name="flutterwave_secret_key" value="{{ $settings['flutterwave_secret_key'] ?? '' }}" placeholder="FLWSECK_TEST-..." class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-amber-400">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">Encryption Key</label>
                                <input type="text" name="flutterwave_encryption_key" value="{{ $settings['flutterwave_encryption_key'] ?? '' }}" placeholder="FLWSECK_TEST..." class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-amber-400">
                            </div>
                        </div>
                    </div>
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