@extends('layouts.app', ['title' => 'Device Profile'])

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Back Link -->
    <div>
        <a href="{{ route('admin.devices.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Devices Fleet
        </a>
    </div>

    <!-- Device Overview Card -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl space-y-6 border border-white/10 shadow-2xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-[#FF2E4C] to-[#FF6B6B] flex items-center justify-center text-white shadow-xl shadow-[#FF2E4C]/30 text-2xl font-black flex-shrink-0">
                    {{ strtoupper(substr($device->display_name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-2xl font-black text-white tracking-tight">{{ $device->display_name }}</h1>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">Auto Guest Account</span>
                        @if($device->isOnline())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                ONLINE
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-400">OFFLINE</span>
                        @endif
                    </div>
                    <div class="text-xs text-slate-400 font-mono mt-1">
                        {{ $device->device_name ?: 'Mobile Client' }} • ID: {{ $device->device_id }}
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <div class="p-3.5 rounded-2xl bg-white/5 border border-white/5 text-center min-w-[90px]">
                    <div class="text-[10px] uppercase text-slate-400 font-semibold">Total Sessions</div>
                    <div class="text-lg font-bold text-white mt-0.5">{{ $device->sessions_count }}</div>
                </div>
                <div class="p-3.5 rounded-2xl bg-white/5 border border-white/5 text-center min-w-[90px]">
                    <div class="text-[10px] uppercase text-slate-400 font-semibold">Watch Telemetry</div>
                    <div class="text-lg font-bold text-[#FF2E4C] mt-0.5">{{ $watchHistory->count() }}</div>
                </div>
                <div class="p-3.5 rounded-2xl bg-white/5 border border-white/5 text-center min-w-[90px]">
                    <div class="text-[10px] uppercase text-slate-400 font-semibold">Watch Time</div>
                    <div class="text-lg font-bold text-amber-400 mt-0.5">{{ $device->formatted_watch_time }}</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 pt-6 border-t border-white/5 text-xs">
            <div>
                <span class="text-slate-500 block text-[11px]">Platform</span>
                <span class="font-bold text-slate-200 uppercase">{{ $device->platform }}</span>
            </div>
            <div>
                <span class="text-slate-500 block text-[11px]">OS Version</span>
                <span class="font-bold text-slate-200">{{ $device->os_version ?: '1.0' }}</span>
            </div>
            <div>
                <span class="text-slate-500 block text-[11px]">App Version</span>
                <span class="font-bold text-slate-200">{{ $device->app_version ?: '1.0.0' }}</span>
            </div>
            <div>
                <span class="text-slate-500 block text-[11px]">IP Address</span>
                <span class="font-bold font-mono text-slate-200">{{ $device->ip_address ?: '127.0.0.1' }}</span>
            </div>
            <div>
                <span class="text-slate-500 block text-[11px]">Last Active</span>
                <span class="font-bold text-slate-200">{{ $device->last_active_at ? $device->last_active_at->diffForHumans() : 'Never' }}</span>
            </div>
        </div>
    </div>

    <!-- Playback Watch History Section -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl space-y-6 border border-white/10">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-[#FF2E4C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Detailed Watch History & Progress ({{ $watchHistory->count() }})
            </h2>
            <span class="text-xs text-slate-400">Synced directly from mobile client</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-white/10 text-slate-400 uppercase tracking-wider text-[11px]">
                        <th class="pb-3 font-semibold">Title</th>
                        <th class="pb-3 font-semibold">Type</th>
                        <th class="pb-3 font-semibold">Playback Checkpoint</th>
                        <th class="pb-3 font-semibold">Completion</th>
                        <th class="pb-3 font-semibold text-right">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($watchHistory as $item)
                        @php
                            $pct = $item->duration > 0 ? min(100, round(($item->current_time / $item->duration) * 100)) : 0;
                        @endphp
                        <tr>
                            <td class="py-3 font-semibold text-white flex items-center gap-3">
                                @if($item->poster)
                                    <img src="{{ $item->poster }}" class="w-8 h-12 object-cover rounded-lg bg-white/5 flex-shrink-0">
                                @else
                                    <div class="w-8 h-12 rounded-lg bg-white/5 flex items-center justify-center text-slate-500 text-xs flex-shrink-0">🎬</div>
                                @endif
                                <div>
                                    <div class="text-white font-bold">{{ $item->title }}</div>
                                    @if($item->media_type === 'tv' && $item->season)
                                        <div class="text-[10px] text-purple-300">Season {{ $item->season }}, Episode {{ $item->episode }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3">
                                @if($item->media_type === 'tv')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/20 text-purple-300">
                                        TV SERIES
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-300">
                                        MOVIE
                                    </span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="w-40">
                                    <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1 font-mono">
                                        <span>{{ gmdate('H:i:s', (int)$item->current_time) }}</span>
                                        <span>{{ $pct }}%</span>
                                    </div>
                                    <div class="w-full h-1.5 rounded-full bg-white/10 overflow-hidden">
                                        <div class="h-full bg-[#FF2E4C] rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                @if($item->completed || $pct >= 90)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Completed</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">In Progress</span>
                                @endif
                            </td>
                            <td class="py-3 text-right text-slate-400 font-mono text-[11px]">
                                {{ $item->last_watched_at ? $item->last_watched_at->format('M d, Y H:i') : 'Just now' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">
                                No playback telemetry recorded on this device yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
