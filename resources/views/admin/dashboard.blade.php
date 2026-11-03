@extends('layouts.app', ['title' => 'Dashboard Overview'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    <!-- HERO BANNER: AdMob Quick Control & Live Server -->
    <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8 bg-gradient-to-r from-[#181B26] via-[#14161F] to-[#0E1017] border border-white/10 shadow-2xl">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-[#FF2E4C]/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-32 -top-12 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-xl">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Live on moviecat.djstube.com
                    </span>
                    <span class="text-xs text-slate-400 font-mono">Global Device Fleet</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Device Fleet & Telemetry Control Center
                </h2>
                <p class="text-slate-400 text-xs sm:text-sm">
                    Users get automatic usernames upon app launch. Track global user countries, playback sessions, app error telemetry, and toggle AdMob ads globally.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('admin.admob.toggle') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-3.5 rounded-2xl text-xs font-bold transition-all shadow-xl flex items-center gap-2.5 {{ $adsEnabled ? 'bg-[#FF2E4C] hover:bg-[#ff1738] text-white shadow-[#FF2E4C]/30' : 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-emerald-500/30' }}">
                        @if($adsEnabled)
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            <span>Turn Off AdMob</span>
                        @else
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Turn On AdMob</span>
                        @endif
                    </button>
                </form>
                <a href="{{ route('admin.admob.index') }}" class="px-4 py-3.5 rounded-2xl bg-white/5 hover:bg-white/10 text-xs font-semibold text-slate-300 border border-white/10 transition-colors">
                    AdMob Config &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.devices.index', ['status' => 'online']) }}" class="glass-panel p-5 rounded-3xl hover:border-emerald-500/30 transition-all block group">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Active Right Now</span>
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            </div>
            <div class="text-3xl font-extrabold text-white mt-2 group-hover:text-emerald-400 transition-colors">{{ $stats['activeNowDevices'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Online now • {{ $stats['activeTodayDevices'] }} in 24h &rarr;</div>
        </a>

        <a href="{{ route('admin.devices.index') }}" class="glass-panel p-5 rounded-3xl hover:border-[#FF2E4C]/30 transition-all block group">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Guest Fleet</div>
            <div class="text-3xl font-extrabold text-[#FF2E4C] mt-2">{{ $stats['totalDevices'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">{{ $stats['totalCountries'] }} countries • {{ $stats['androidDevices'] }} Android • {{ $stats['iosDevices'] }} iOS &rarr;</div>
        </a>

        <a href="{{ route('admin.sync.index') }}" class="glass-panel p-5 rounded-3xl hover:border-amber-500/30 transition-all block group">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Watch Checkpoints</div>
            <div class="text-3xl font-extrabold text-amber-400 mt-2">{{ $stats['totalProgress'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">{{ $stats['completedWatches'] }} finished streams &rarr;</div>
        </a>

        <a href="{{ route('admin.errors.index') }}" class="glass-panel p-5 rounded-3xl hover:border-rose-500/30 transition-all block group">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>App Error Telemetry</span>
                @if($stats['unresolvedErrors'] > 0)
                    <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
                @endif
            </div>
            <div class="text-3xl font-extrabold text-rose-400 mt-2">{{ $stats['unresolvedErrors'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Unresolved issues • {{ $stats['totalErrors'] }} total &rarr;</div>
        </a>
    </div>

    <!-- Country Distribution Breakdown -->
    <div class="glass-panel p-6 sm:p-7 rounded-3xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-lg">🌍</span>
                <span>Active User Countries Breakdown</span>
            </h3>
            <span class="text-xs text-slate-400 font-mono">{{ $stats['totalCountries'] }} Countries Detected</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
            @forelse($topCountries as $tc)
                @php
                    $code = strtoupper($tc->country_code ?: '');
                    $flag = (strlen($code) === 2 && ctype_alpha($code))
                        ? mb_chr(127397 + ord($code[0]), 'UTF-8') . mb_chr(127397 + ord($code[1]), 'UTF-8')
                        : '🌐';
                @endphp
                <a href="{{ route('admin.devices.index', ['country' => $tc->country_code]) }}" class="p-3.5 rounded-2xl bg-white/[0.03] border border-white/5 hover:border-white/20 transition-all text-center block group">
                    <div class="text-2xl">{{ $flag }}</div>
                    <div class="text-xs font-bold text-white mt-1 group-hover:text-emerald-400 transition-colors truncate">{{ $tc->country ?: $tc->country_code }}</div>
                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $tc->count }} {{ Str::plural('device', $tc->count) }}</div>
                </a>
            @empty
                <div class="col-span-6 py-4 text-center text-xs text-slate-400">
                    No country telemetry captured yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Two Column: Recent Devices Preview & Recent Watch Stream -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Devices -->
        <div class="glass-panel p-6 sm:p-7 rounded-3xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Recent Active Devices
                </h3>
                <a href="{{ route('admin.devices.index') }}" class="text-xs text-[#FF2E4C] hover:underline font-semibold">View All Fleet &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($recentDevices as $device)
                    <div class="p-3.5 rounded-2xl bg-white/[0.03] border border-white/5 flex items-center justify-between text-xs hover:bg-white/[0.05] transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-slate-800 to-slate-700 flex items-center justify-center text-white font-extrabold text-xs shadow">
                                {{ strtoupper(substr($device->display_name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-white">{{ $device->display_name }}</span>
                                    <span class="text-xs" title="{{ $device->country }}">{{ $device->country_flag }}</span>
                                    @if($device->isOnline())
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">ONLINE</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                    {{ $device->device_name ?: 'Mobile' }} • {{ $device->country ?: 'Global' }} • {{ strtoupper($device->platform) }}
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.devices.show', $device->device_id) }}" class="px-3 py-1.5 rounded-xl bg-white/5 hover:bg-[#FF2E4C] text-[#FF2E4C] hover:text-white font-semibold text-xs transition-colors">
                            Inspect
                        </a>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">No active devices registered yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Watch Progress -->
        <div class="glass-panel p-6 sm:p-7 rounded-3xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Live Playback Stream
                </h3>
                <a href="{{ route('admin.sync.index') }}" class="text-xs text-[#FF2E4C] hover:underline font-semibold">View Full History &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($recentSyncs as $sync)
                    @php
                        $pct = $sync->duration > 0 ? min(100, round(($sync->current_time / $sync->duration) * 100)) : 0;
                    @endphp
                    <div class="p-3.5 rounded-2xl bg-white/[0.03] border border-white/5 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3 truncate">
                            @if($sync->poster)
                                <img src="{{ $sync->poster }}" class="w-8 h-11 object-cover rounded-lg bg-white/5 flex-shrink-0">
                            @else
                                <div class="w-8 h-11 rounded-lg bg-white/5 flex items-center justify-center text-slate-500 text-[10px] flex-shrink-0">🎬</div>
                            @endif
                            <div class="truncate">
                                <div class="font-bold text-white truncate">{{ $sync->title }}</div>
                                <div class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                    <span class="text-emerald-400 font-medium">{{ $sync->device->display_name ?? 'Guest User' }}</span>
                                    <span>{{ $sync->device?->country_flag }}</span>
                                    <span>•</span>
                                    <span>{{ $sync->media_type === 'tv' ? "S{$sync->season}:E{$sync->episode}" : 'Movie' }}</span>
                                    <span>•</span>
                                    <span>{{ $pct }}%</span>
                                </div>
                            </div>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono flex-shrink-0 ml-2">
                            {{ $sync->last_watched_at ? $sync->last_watched_at->diffForHumans() : 'Just now' }}
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">No playback stream received yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
