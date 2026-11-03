@extends('layouts.app', ['title' => 'Executive Analytics & Telemetry'])

@section('content')
<div class="space-y-6">

    <!-- HERO HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 sm:p-8 rounded-3xl glass-panel relative overflow-hidden">
        <div class="space-y-1 relative z-10">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    Live Mobile Fleet Active
                </span>
                <span class="text-xs text-slate-400">• Real-Time Analytics</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <span>Executive Telemetry & Metrics</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 max-w-2xl">
                Global user fleet telemetry, active devices, total watch time, offline downloads, ad delivery, and client health monitoring.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 relative z-10">
            <!-- Wipe Mock Data Button -->
            <form action="{{ route('admin.wipe-mock') }}" method="POST" onsubmit="return confirm('Wipe all mock/test devices and history from the database? Real incoming devices will start recording cleanly.');">
                @csrf
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-white/5 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-white/10 hover:border-rose-500/30 text-xs font-bold transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Wipe Mock Data</span>
                </button>
            </form>

            <a href="{{ route('admin.devices.index') }}" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-lg shadow-rose-600/20 transition-all flex items-center gap-2">
                <span>Inspect Devices</span>
                <span class="text-xs">→</span>
            </a>
        </div>
    </div>

    <!-- PRIMARY METRIC CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Connected Devices -->
        <div class="p-5 rounded-2xl glass-panel relative overflow-hidden group">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Registered Devices</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-white mt-1">{{ number_format($stats['totalDevices']) }}</div>
            <div class="text-[11px] text-emerald-400 mt-1 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>{{ $stats['activeNowDevices'] }} online now ({{ $stats['activeTodayDevices'] }} today)</span>
            </div>
        </div>

        <!-- Total Watch Time -->
        <div class="p-5 rounded-2xl glass-panel relative overflow-hidden group">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Watch Time</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-purple-400 mt-1">{{ $stats['totalWatchTime'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">{{ number_format($stats['totalProgress']) }} stream checkpoints</div>
        </div>

        <!-- Total Downloads -->
        <div class="p-5 rounded-2xl glass-panel relative overflow-hidden group">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Offline Downloads</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-orange-400 mt-1">{{ number_format($stats['totalDownloads']) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">{{ $stats['totalDataTransferred'] }} offline storage</div>
        </div>

        <!-- Countries & Reach -->
        <div class="p-5 rounded-2xl glass-panel relative overflow-hidden group">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Global Reach</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-sky-400 mt-1">{{ $stats['totalCountries'] }} Countries</div>
            <div class="text-[11px] text-slate-400 mt-1">{{ $stats['androidDevices'] }} Android • {{ $stats['iosDevices'] }} iOS</div>
        </div>
    </div>

    <!-- TOP COUNTRIES BREAKDOWN CARDS -->
    <div class="p-6 rounded-3xl glass-panel space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span>🌍 Top User Countries & Engagement</span>
                </h3>
                <p class="text-xs text-slate-400">Distribution of devices and aggregate watch time by country.</p>
            </div>
            <a href="{{ route('admin.devices.index') }}" class="text-xs text-rose-400 hover:text-rose-300 font-semibold">View All Devices →</a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @forelse($topCountries as $c)
                @php
                    $flag = '';
                    if ($c->country_code && strlen($c->country_code) === 2) {
                        $c1 = ord(strtoupper($c->country_code[0])) - 65 + 0x1F1E6;
                        $c2 = ord(strtoupper($c->country_code[1])) - 65 + 0x1F1E6;
                        $flag = mb_chr($c1, 'UTF-8') . mb_chr($c2, 'UTF-8');
                    }
                @endphp
                <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">{{ $flag ?: '🌐' }}</span>
                        <span class="text-[10px] font-mono text-slate-400 font-bold uppercase">{{ $c->country_code ?: 'INTL' }}</span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white truncate">{{ $c->country ?: 'Global' }}</div>
                        <div class="text-sm font-extrabold text-white mt-0.5">{{ $c->count }} <span class="text-[10px] font-normal text-slate-400">devices</span></div>
                    </div>
                    <div class="w-full bg-white/5 rounded-full h-1 overflow-hidden">
                        <div class="bg-gradient-to-r from-rose-500 to-orange-500 h-full rounded-full" style="width: {{ $c->percentage }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-400">
                        <span>{{ $c->percentage }}%</span>
                        <span class="font-mono text-purple-300">{{ $c->formatted_watch_time }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-6 text-center text-xs text-slate-400">
                    No country data yet. Open the mobile app to register your location!
                </div>
            @endforelse
        </div>
    </div>

    <!-- MOST WATCHED & MOST DOWNLOADED LEADERBOARDS (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Most Watched Content -->
        <div class="p-6 rounded-3xl glass-panel space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="text-lg">📺</span>
                    <span>Most Watched Content</span>
                </h3>
                <span class="text-[10px] font-mono text-slate-400 uppercase tracking-wider">Live Telemetry</span>
            </div>

            <div class="space-y-3">
                @forelse($mostWatchedMovies->merge($mostWatchedSeries)->take(5) as $idx => $item)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-white/[0.02] border border-white/5 hover:bg-white/[0.05] transition-all">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-xs font-bold text-white">#{{ $idx + 1 }}</span>
                            @if($item->poster)
                                <img src="{{ $item->poster }}" class="w-9 h-12 rounded-lg object-cover bg-slate-800" alt="{{ $item->title }}" />
                            @else
                                <div class="w-9 h-12 rounded-lg bg-slate-800 flex items-center justify-center text-[10px] text-slate-400">N/A</div>
                            @endif
                            <div class="overflow-hidden">
                                <div class="text-xs font-bold text-white truncate max-w-[180px] sm:max-w-xs">{{ $item->title }}</div>
                                <div class="text-[10px] text-purple-400 font-mono">{{ $item->formatted_time }} total watch time</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-500/15 text-purple-400 border border-purple-500/20">
                            {{ $item->count }} sessions
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">No playback sessions recorded yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Most Downloaded Media -->
        <div class="p-6 rounded-3xl glass-panel space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="text-lg">💾</span>
                    <span>Most Downloaded for Offline</span>
                </h3>
                <a href="{{ route('admin.downloads.index') }}" class="text-xs text-orange-400 hover:text-orange-300 font-semibold">View All Downloads →</a>
            </div>

            <div class="space-y-3">
                @forelse($mostDownloaded as $idx => $item)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-white/[0.02] border border-white/5 hover:bg-white/[0.05] transition-all">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-xs font-bold text-white">#{{ $idx + 1 }}</span>
                            @if($item->poster)
                                <img src="{{ $item->poster }}" class="w-9 h-12 rounded-lg object-cover bg-slate-800" alt="{{ $item->title }}" />
                            @else
                                <div class="w-9 h-12 rounded-lg bg-slate-800 flex items-center justify-center text-[10px] text-slate-400">N/A</div>
                            @endif
                            <div class="overflow-hidden">
                                <div class="text-xs font-bold text-white truncate max-w-[180px] sm:max-w-xs">{{ $item->title }}</div>
                                <div class="text-[10px] text-orange-400 font-mono">{{ $item->formatted_size }} saved offline</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-orange-500/15 text-orange-400 border border-orange-500/20">
                            {{ $item->count }} downloads
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">No offline downloads recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- RECENT DEVICES & PLAYBACK STREAM -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recently Active Devices -->
        <div class="p-6 rounded-3xl glass-panel space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Recent Active Devices</span>
                </h3>
                <a href="{{ route('admin.devices.index') }}" class="text-xs text-rose-400 hover:text-rose-300 font-semibold">View Fleet →</a>
            </div>

            <div class="space-y-3">
                @forelse($recentDevices as $device)
                    <a href="{{ route('admin.devices.show', $device->device_id) }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-white/[0.02] border border-white/5 hover:bg-white/[0.05] transition-all group">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">{{ $device->country_flag }}</span>
                            <div>
                                <div class="text-xs font-bold text-white group-hover:text-rose-400 transition-colors">
                                    {{ $device->display_name }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $device->device_name ?: 'Mobile Device' }} • {{ $device->country_display }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-mono text-slate-400">{{ $device->last_active_at ? $device->last_active_at->diffForHumans() : 'Just now' }}</span>
                        </div>
                    </a>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">No devices connected yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Watch Progress -->
        <div class="p-6 rounded-3xl glass-panel space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                    <span>Live Playback Stream</span>
                </h3>
                <a href="{{ route('admin.sync.index') }}" class="text-xs text-purple-400 hover:text-purple-300 font-semibold">View All →</a>
            </div>

            <div class="space-y-3">
                @forelse($recentSyncs as $item)
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-white/[0.02] border border-white/5">
                        <div class="flex items-center gap-3">
                            @if($item->poster)
                                <img src="{{ $item->poster }}" class="w-8 h-10 rounded object-cover bg-slate-800" alt="{{ $item->title }}" />
                            @endif
                            <div>
                                <div class="text-xs font-bold text-white truncate max-w-[200px]">{{ $item->title }}</div>
                                <div class="text-[10px] text-slate-400">{{ $item->device->username ?? 'Guest' }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item->completed ? 'bg-emerald-500/15 text-emerald-400' : 'bg-purple-500/15 text-purple-400' }}">
                                {{ $item->completed ? 'Completed' : 'Watching' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">No playback activity yet.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
