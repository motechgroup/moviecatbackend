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