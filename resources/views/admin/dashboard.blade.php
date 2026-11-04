@extends('layouts.app', ['title' => 'Executive Analytics & Telemetry'])

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- HERO HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-6 sm:p-8 rounded-3xl glass-panel relative overflow-hidden border border-white/10 shadow-2xl">
        <!-- Background subtle glow -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-gradient-to-br from-rose-500/10 via-purple-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>

        <div class="space-y-2 relative z-10">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Live Mobile Fleet Active
                </span>
                <span class="text-xs text-slate-400 font-mono">• Real-Time Telemetry Stream</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <span>Executive Telemetry & Metrics</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 max-w-2xl leading-relaxed">
                Global user fleet telemetry, active devices, total watch velocity, offline vault downloads, ad delivery, and client health monitoring.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 relative z-10">
            <!-- Feature Badges -->
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 border border-white/5 text-xs text-slate-300">
                <span class="w-2 h-2 rounded-full {{ $downloadsEnabled ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                <span>Downloads: <strong>{{ $downloadsEnabled ? 'Active' : 'Off' }}</strong></span>
            </div>

            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 border border-white/5 text-xs text-slate-300">
                <span class="w-2 h-2 rounded-full {{ $adsEnabled ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                <span>AdMob: <strong>{{ $adsEnabled ? 'Enabled' : 'Disabled' }}</strong></span>
            </div>

            <!-- Wipe Mock Data -->
            <form action="{{ route('admin.wipe-mock') }}" method="POST" onsubmit="return confirm('Wipe all mock/test devices and history from the database? Real incoming devices will start recording cleanly.');">
                @csrf
                <button type="submit" class="px-3.5 py-2.5 rounded-xl bg-white/5 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-white/10 hover:border-rose-500/30 text-xs font-bold transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Wipe Mock Data</span>
                </button>
            </form>

            <!-- Inspect Devices -->
            <a href="{{ route('admin.devices.index') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-600 text-white text-xs font-bold shadow-lg shadow-rose-600/30 transition-all flex items-center gap-1.5">
                <span>Inspect Devices</span>
                <span class="text-xs">&rarr;</span>
            </a>
        </div>
    </div>

    <!-- PRIMARY EXECUTIVE KPI CARDS (BENTO GRID) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- 1. Registered Devices Fleet -->
        <a href="{{ route('admin.devices.index') }}" class="p-5 rounded-3xl glass-panel relative overflow-hidden group hover:border-rose-500/40 transition-all duration-300 block">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Registered Devices</span>
                <div class="w-7 h-7 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
            </div>
            
            <div class="text-3xl font-black text-white mt-2 tracking-tight">
                {{ number_format($stats['totalDevices']) }}
            </div>

            <!-- Ecosystem progress bar -->
            <div class="space-y-1.5 mt-3 pt-3 border-t border-white/5">
                <div class="flex items-center justify-between text-[11px]">
                    <span class="text-emerald-400 font-bold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        {{ $stats['activeNowDevices'] }} online now
                    </span>
                    <span class="text-slate-400 font-mono">{{ $stats['activeTodayDevices'] }} today</span>
                </div>
                <div class="w-full bg-white/5 rounded-full h-1.5 flex overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full" style="width: {{ $platformBreakdown['android_pct'] }}%" title="Android: {{ $platformBreakdown['android_pct'] }}%"></div>
                    <div class="bg-sky-500 h-full" style="width: {{ $platformBreakdown['ios_pct'] }}%" title="iOS: {{ $platformBreakdown['ios_pct'] }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-500 font-mono">
                    <span>Android: {{ $platformBreakdown['android'] }} ({{ $platformBreakdown['android_pct'] }}%)</span>
                    <span>iOS: {{ $platformBreakdown['ios'] }}</span>
                </div>
            </div>
        </a>

        <!-- 2. Total Watch Time & Velocity -->
        <a href="{{ route('admin.sync.index') }}" class="p-5 rounded-3xl glass-panel relative overflow-hidden group hover:border-purple-500/40 transition-all duration-300 block">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Total Watch Time</span>
                <div class="w-7 h-7 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="text-3xl font-black text-purple-400 mt-2 tracking-tight">
                {{ $stats['totalWatchTime'] }}
            </div>

            <div class="space-y-1.5 mt-3 pt-3 border-t border-white/5">
                <div class="flex items-center justify-between text-[11px]">
                    <span class="text-slate-300 font-semibold">{{ number_format($stats['totalProgress']) }} stream checkpoints</span>
                    <span class="text-purple-300 font-mono font-bold">{{ $completionRate }}% finished</span>
                </div>
                <div class="w-full bg-white/5 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-indigo-500 h-full rounded-full" style="width: {{ $completionRate }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-500 font-mono">
                    <span>{{ $stats['completedWatches'] }} completed full watches</span>
                    <span class="text-purple-400 group-hover:underline">Stream logs &rarr;</span>
                </div>
            </div>
        </a>

        <!-- 3. Vault Offline Downloads -->
        <a href="{{ route('admin.downloads.index') }}" class="p-5 rounded-3xl glass-panel relative overflow-hidden group hover:border-orange-500/40 transition-all duration-300 block">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Vault Downloads</span>
                <div class="w-7 h-7 rounded-xl bg-orange-500/10 text-orange-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </div>
            </div>

            <div class="text-3xl font-black text-orange-400 mt-2 tracking-tight">
                {{ number_format($stats['totalDownloads']) }}
            </div>

            <div class="space-y-1.5 mt-3 pt-3 border-t border-white/5">
                <div class="flex items-center justify-between text-[11px]">
                    <span class="text-slate-300 font-semibold">{{ $stats['totalDataTransferred'] }} offline storage saved</span>
                    <span class="text-orange-400 font-mono">{{ $downloadsEnabled ? 'Active' : 'Paused' }}</span>
                </div>
                <div class="w-full bg-white/5 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500 to-amber-400 h-full rounded-full" style="width: {{ $stats['totalDownloads'] > 0 ? 100 : 0 }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-500 font-mono">
                    <span>Secure client vaults</span>
                    <span class="text-orange-400 group-hover:underline">Inspect vaults &rarr;</span>
                </div>
            </div>
        </a>

        <!-- 4. Client App Health & Stability -->
        <a href="{{ route('admin.errors.index') }}" class="p-5 rounded-3xl glass-panel relative overflow-hidden group hover:border-sky-500/40 transition-all duration-300 block">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Fleet Health Score</span>
                <div class="w-7 h-7 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>

            <div class="text-3xl font-black {{ $healthScore >= 90 ? 'text-emerald-400' : ($healthScore >= 75 ? 'text-amber-400' : 'text-rose-400') }} mt-2 tracking-tight">
                {{ $healthScore }}%
            </div>

            <div class="space-y-1.5 mt-3 pt-3 border-t border-white/5">
                <div class="flex items-center justify-between text-[11px]">
                    <span class="text-slate-300 font-semibold">{{ $stats['unresolvedErrors'] }} open error logs</span>
                    <span class="text-rose-400 font-mono font-bold">{{ $stats['totalErrors'] }} total</span>
                </div>
                <div class="w-full bg-white/5 rounded-full h-1.5 overflow-hidden">
                    <div class="h-full rounded-full {{ $healthScore >= 90 ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ $healthScore }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-500 font-mono">
                    <span>{{ $stats['totalCountries'] }} countries reachable</span>
                    <span class="text-sky-400 group-hover:underline">Debug Center &rarr;</span>
                </div>
            </div>
        </a>
    </div>

    <!-- VISUAL ANALYTICS: CHARTS ROW (7-Day Activity Velocity & Ecosystem Donut) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Chart 1: Streaming Velocity Trend (2 Cols) -->
        <div class="lg:col-span-2 p-6 sm:p-7 rounded-3xl glass-panel space-y-4 relative overflow-hidden border border-white/10 shadow-2xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        <span>7-Day Fleet Streaming & Activity Velocity</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Daily watch duration, sync checkpoints, and vault downloads captured from active client devices.</p>
                </div>

                <!-- Metric Toggle Pills -->
                <div class="flex items-center gap-1 p-1 rounded-xl bg-white/5 border border-white/10 text-xs self-start sm:self-auto">
                    <button type="button" 
                            id="btnChartWatch"
                            onclick="switchChartMetric('watch')" 
                            class="px-3 py-1.5 rounded-lg font-bold text-xs bg-rose-500 text-white shadow-md transition-all">
                        Watch Minutes
                    </button>
                    <button type="button" 
                            id="btnChartSync"
                            onclick="switchChartMetric('sync')" 
                            class="px-3 py-1.5 rounded-lg font-bold text-xs text-slate-400 hover:text-white transition-all">
                        Checkpoints
                    </button>
                    <button type="button" 
                            id="btnChartDl"
                            onclick="switchChartMetric('dl')" 
                            class="px-3 py-1.5 rounded-lg font-bold text-xs text-slate-400 hover:text-white transition-all">
                        Downloads
                    </button>
                </div>
            </div>

            <!-- Canvas Container -->
            <div class="relative h-64 sm:h-72 w-full pt-2">
                <canvas id="velocityChart"></canvas>
            </div>

            <!-- Summary Footnote -->
            <div class="grid grid-cols-3 gap-2 pt-3 border-t border-white/5 text-center text-xs">
                <div class="p-2 rounded-xl bg-white/[0.02]">
                    <div class="text-[10px] text-slate-400 uppercase font-semibold">Total Watch Minutes</div>
                    <div class="text-sm font-extrabold text-rose-400 mt-0.5">{{ round($stats['totalWatchSeconds'] / 60, 1) }} min</div>
                </div>
                <div class="p-2 rounded-xl bg-white/[0.02]">
                    <div class="text-[10px] text-slate-400 uppercase font-semibold">Activity Checkpoints</div>
                    <div class="text-sm font-extrabold text-purple-400 mt-0.5">{{ number_format($stats['totalProgress']) }}</div>
                </div>
                <div class="p-2 rounded-xl bg-white/[0.02]">
                    <div class="text-[10px] text-slate-400 uppercase font-semibold">Offline Vault Files</div>
                    <div class="text-sm font-extrabold text-orange-400 mt-0.5">{{ number_format($stats['totalDownloads']) }}</div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Client Fleet Ecosystem & Platform Share (1 Col) -->
        <div class="p-6 sm:p-7 rounded-3xl glass-panel space-y-4 relative overflow-hidden border border-white/10 shadow-2xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                        <span>Platform Breakdown</span>
                    </h3>
                    <a href="{{ route('admin.devices.index') }}" class="text-xs text-sky-400 hover:text-sky-300 font-semibold">Fleet &rarr;</a>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Operating systems powering registered mobile devices.</p>
            </div>

            <!-- Donut Canvas Container -->
            <div class="relative h-48 w-full flex items-center justify-center my-2">
                <canvas id="platformDonutChart"></canvas>
            </div>

            <!-- Ecosystem Breakdown List -->
            <div class="space-y-2 pt-3 border-t border-white/5 text-xs">
                <div class="flex items-center justify-between p-2 rounded-xl bg-white/[0.02] border border-white/5">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                        <span class="font-bold text-white">Android Mobile</span>
                    </div>
                    <div class="font-mono text-emerald-400 font-bold">
                        {{ $platformBreakdown['android'] }} ({{ $platformBreakdown['android_pct'] }}%)
                    </div>
                </div>

                <div class="flex items-center justify-between p-2 rounded-xl bg-white/[0.02] border border-white/5">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-400"></span>
                        <span class="font-bold text-white">iOS Mobile</span>
                    </div>
                    <div class="font-mono text-sky-400 font-bold">
                        {{ $platformBreakdown['ios'] }} ({{ $platformBreakdown['ios_pct'] }}%)
                    </div>
                </div>

                <div class="flex items-center justify-between p-2 rounded-xl bg-white/[0.02] border border-white/5">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span>
                        <span class="font-bold text-white">Other / Unknown</span>
                    </div>
                    <div class="font-mono text-purple-400 font-bold">
                        {{ $platformBreakdown['other'] }} ({{ $platformBreakdown['other_pct'] }}%)
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TOP COUNTRIES & REGIONAL REACH -->
    <div class="p-6 sm:p-7 rounded-3xl glass-panel space-y-4 border border-white/10 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                    <span>🌍 Geographic Reach & Regional Engagement</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Distribution of user fleet and aggregate watch duration across active countries.</p>
            </div>
            <a href="{{ route('admin.devices.index') }}" class="text-xs text-rose-400 hover:text-rose-300 font-semibold">View All Devices &rarr;</a>
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
                <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 hover:border-white/15 hover:bg-white/[0.04] transition-all space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">{{ $flag ?: '🌐' }}</span>
                        <span class="text-[10px] font-mono text-slate-400 font-bold uppercase bg-white/5 px-2 py-0.5 rounded">{{ $c->country_code ?: 'INTL' }}</span>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white truncate">{{ $c->country ?: 'Global' }}</div>
                        <div class="text-sm font-extrabold text-white mt-0.5">{{ $c->count }} <span class="text-[10px] font-normal text-slate-400">devices</span></div>
                    </div>
                    <div class="w-full bg-white/5 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-rose-500 to-orange-500 h-full rounded-full" style="width: {{ $c->percentage }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono">
                        <span>{{ $c->percentage }}% share</span>
                        <span class="text-purple-300 font-bold">{{ $c->formatted_watch_time }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-xs text-slate-400">
                    No country telemetry captured yet. Devices will automatically populate geographic telemetry upon launch!
                </div>
            @endforelse
        </div>
    </div>

    <!-- LEADERBOARDS: TABBED MEDIA CONSUMPTION (Movies, Series, Vault Downloads) -->
    <div class="p-6 sm:p-7 rounded-3xl glass-panel space-y-5 border border-white/10 shadow-2xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                    <span>🔥 Top Media Consumption & Engagement</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Most streamed movies, television series, and media downloaded to offline client vaults.</p>
            </div>

            <!-- Tab Switcher -->
            <div class="flex items-center gap-1.5 p-1 rounded-2xl bg-white/5 border border-white/10 text-xs self-start sm:self-auto">
                <button type="button" 
                        id="tabBtnMovies" 
                        onclick="switchMediaTab('movies')" 
                        class="px-3.5 py-1.5 rounded-xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md transition-all">
                    🎬 Top Movies
                </button>
                <button type="button" 
                        id="tabBtnSeries" 
                        onclick="switchMediaTab('series')" 
                        class="px-3.5 py-1.5 rounded-xl font-bold text-slate-400 hover:text-white transition-all">
                    📺 Top Series
                </button>
                <button type="button" 
                        id="tabBtnVault" 
                        onclick="switchMediaTab('vault')" 
                        class="px-3.5 py-1.5 rounded-xl font-bold text-slate-400 hover:text-white transition-all">
                    💾 Vault Downloads
                </button>
            </div>
        </div>

        <!-- TAB 1: MOVIES -->
        <div id="mediaTabMovies" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($mostWatchedMovies as $idx => $item)
                <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/5 hover:border-purple-500/30 hover:bg-white/[0.04] transition-all flex items-center justify-between gap-3 group">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <span class="w-7 h-7 rounded-xl font-bold text-xs flex items-center justify-center shrink-0 {{ $idx === 0 ? 'bg-amber-400/20 text-amber-300 border border-amber-400/30' : ($idx === 1 ? 'bg-slate-300/20 text-slate-200 border border-slate-300/30' : 'bg-white/5 text-slate-400 border border-white/10') }}">
                            #{{ $idx + 1 }}
                        </span>
                        @if($item->poster)
                            <img src="{{ $item->poster }}" class="w-10 h-14 rounded-xl object-cover bg-slate-800 shrink-0 group-hover:scale-105 transition-transform" alt="{{ $item->title }}" />
                        @else
                            <div class="w-10 h-14 rounded-xl bg-slate-800 shrink-0 flex items-center justify-center text-[10px] text-slate-400">N/A</div>
                        @endif
                        <div class="overflow-hidden">
                            <div class="text-xs font-bold text-white truncate group-hover:text-purple-300 transition-colors">{{ $item->title }}</div>
                            <div class="text-[10px] text-purple-400 font-mono mt-0.5">{{ $item->formatted_time }} streamed</div>
                            <div class="text-[9px] text-slate-500 font-mono mt-0.5">Media ID: {{ $item->media_id }}</div>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-500/15 text-purple-300 border border-purple-500/30 shrink-0">
                        {{ $item->count }} views
                    </span>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-xs text-slate-400">
                    No movie playback streams recorded yet. Streams will automatically log telemetry when users watch movies in the mobile app.
                </div>
            @endforelse
        </div>

        <!-- TAB 2: SERIES -->
        <div id="mediaTabSeries" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($mostWatchedSeries as $idx => $item)
                <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/5 hover:border-sky-500/30 hover:bg-white/[0.04] transition-all flex items-center justify-between gap-3 group">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <span class="w-7 h-7 rounded-xl font-bold text-xs flex items-center justify-center shrink-0 {{ $idx === 0 ? 'bg-amber-400/20 text-amber-300 border border-amber-400/30' : ($idx === 1 ? 'bg-slate-300/20 text-slate-200 border border-slate-300/30' : 'bg-white/5 text-slate-400 border border-white/10') }}">
                            #{{ $idx + 1 }}
                        </span>
                        @if($item->poster)
                            <img src="{{ $item->poster }}" class="w-10 h-14 rounded-xl object-cover bg-slate-800 shrink-0 group-hover:scale-105 transition-transform" alt="{{ $item->title }}" />
                        @else
                            <div class="w-10 h-14 rounded-xl bg-slate-800 shrink-0 flex items-center justify-center text-[10px] text-slate-400">N/A</div>
                        @endif
                        <div class="overflow-hidden">
                            <div class="text-xs font-bold text-white truncate group-hover:text-sky-300 transition-colors">{{ $item->title }}</div>
                            <div class="text-[10px] text-sky-400 font-mono mt-0.5">{{ $item->formatted_time }} streamed</div>
                            <div class="text-[9px] text-slate-500 font-mono mt-0.5">Media ID: {{ $item->media_id }}</div>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-500/15 text-sky-300 border border-sky-500/30 shrink-0">
                        {{ $item->count }} views
                    </span>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-xs text-slate-400">
                    No TV show series streams recorded yet.
                </div>
            @endforelse
        </div>

        <!-- TAB 3: VAULT DOWNLOADS -->
        <div id="mediaTabVault" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($mostDownloaded as $idx => $item)
                <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/5 hover:border-orange-500/30 hover:bg-white/[0.04] transition-all flex items-center justify-between gap-3 group">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <span class="w-7 h-7 rounded-xl font-bold text-xs flex items-center justify-center shrink-0 {{ $idx === 0 ? 'bg-amber-400/20 text-amber-300 border border-amber-400/30' : ($idx === 1 ? 'bg-slate-300/20 text-slate-200 border border-slate-300/30' : 'bg-white/5 text-slate-400 border border-white/10') }}">
                            #{{ $idx + 1 }}
                        </span>
                        @if($item->poster)
                            <img src="{{ $item->poster }}" class="w-10 h-14 rounded-xl object-cover bg-slate-800 shrink-0 group-hover:scale-105 transition-transform" alt="{{ $item->title }}" />
                        @else
                            <div class="w-10 h-14 rounded-xl bg-slate-800 shrink-0 flex items-center justify-center text-[10px] text-slate-400">N/A</div>
                        @endif
                        <div class="overflow-hidden">
                            <div class="text-xs font-bold text-white truncate group-hover:text-orange-300 transition-colors">{{ $item->title }}</div>
                            <div class="text-[10px] text-orange-400 font-mono mt-0.5">{{ $item->formatted_size }} offline</div>
                            <div class="text-[9px] text-slate-500 font-mono mt-0.5">Media ID: {{ $item->media_id }}</div>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-500/15 text-orange-300 border border-orange-500/30 shrink-0">
                        {{ $item->count }} saved
                    </span>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-xs text-slate-400">
                    No offline vault downloads recorded yet. When users tap download in the mobile app, cached media will populate here automatically.
                </div>
            @endforelse
        </div>
    </div>

    <!-- RECENT ACTIVITY FEEDS (3 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Column 1: Recently Active Devices -->
        <div class="p-6 rounded-3xl glass-panel space-y-4 border border-white/10 shadow-2xl">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Recent Active Devices</span>
                </h3>
                <a href="{{ route('admin.devices.index') }}" class="text-xs text-rose-400 hover:text-rose-300 font-semibold">View Fleet &rarr;</a>
            </div>

            <div class="space-y-2.5">
                @forelse($recentDevices as $device)
                    <a href="{{ route('admin.devices.show', $device->device_id) }}" class="flex items-center justify-between p-3 rounded-2xl bg-white/[0.02] border border-white/5 hover:bg-white/[0.05] transition-all group">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">{{ $device->country_flag ?: '🌐' }}</span>
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

        <!-- Column 2: Live Playback Streams -->
        <div class="p-6 rounded-3xl glass-panel space-y-4 border border-white/10 shadow-2xl">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                    <span>Live Playback Streams</span>
                </h3>
                <a href="{{ route('admin.sync.index') }}" class="text-xs text-purple-400 hover:text-purple-300 font-semibold">View Streams &rarr;</a>
            </div>

            <div class="space-y-2.5">
                @forelse($recentSyncs as $item)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-white/[0.02] border border-white/5">
                        <div class="flex items-center gap-3 overflow-hidden">
                            @if($item->poster)
                                <img src="{{ $item->poster }}" class="w-8 h-10 rounded-lg object-cover bg-slate-800 shrink-0" alt="{{ $item->title }}" />
                            @endif
                            <div class="overflow-hidden">
                                <div class="text-xs font-bold text-white truncate max-w-[140px]">{{ $item->title }}</div>
                                <div class="text-[10px] text-slate-400 truncate">{{ $item->device->display_name ?? 'Guest User' }}</div>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
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

        <!-- Column 3: Offline Vault Downloads -->
        <div class="p-6 rounded-3xl glass-panel space-y-4 border border-white/10 shadow-2xl">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                    <span>Live Vault Downloads</span>
                </h3>
                <a href="{{ route('admin.downloads.index') }}" class="text-xs text-orange-400 hover:text-orange-300 font-semibold">View Vaults &rarr;</a>
            </div>

            <div class="space-y-2.5">
                @forelse($recentDownloads as $dl)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-white/[0.02] border border-white/5">
                        <div class="flex items-center gap-3 overflow-hidden">
                            @if($dl->poster)
                                <img src="{{ $dl->poster }}" class="w-8 h-10 rounded-lg object-cover bg-slate-800 shrink-0" alt="{{ $dl->title }}" />
                            @endif
                            <div class="overflow-hidden">
                                <div class="text-xs font-bold text-white truncate max-w-[140px]">{{ $dl->title }}</div>
                                <div class="text-[10px] text-slate-400 truncate">{{ $dl->device->display_name ?? 'Guest User' }} • {{ $dl->quality }}</div>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-500/15 text-orange-400 border border-orange-500/20 font-mono">
                                {{ number_format($dl->file_size_bytes / (1024 * 1024), 1) }} MB
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">No downloads saved in vaults yet.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- CHART.JS INITIALIZATION & SCRIPT LOGIC -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Data Payloads from Backend
    const trendLabels = @json($trendLabels);
    const trendWatchMins = @json($trendWatchMins);
    const trendCheckpoints = @json($trendCheckpoints);
    const trendDownloads = @json($trendDownloads);
    const platformData = @json($platformBreakdown);

    // 2. Velocity Area Chart Setup
    const ctxVelocity = document.getElementById('velocityChart');
    let velocityChart = null;

    if (ctxVelocity && typeof Chart !== 'undefined') {
        const gradientWatch = ctxVelocity.getContext('2d').createLinearGradient(0, 0, 0, 260);
        gradientWatch.addColorStop(0, 'rgba(244, 63, 94, 0.45)');
        gradientWatch.addColorStop(1, 'rgba(244, 63, 94, 0.0)');

        velocityChart = new Chart(ctxVelocity, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Watch Minutes',
                    data: trendWatchMins,
                    borderColor: '#F43F5E',
                    borderWidth: 3,
                    backgroundColor: gradientWatch,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#F43F5E',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 17, 26, 0.95)',
                        titleColor: '#FFFFFF',
                        bodyColor: '#E2E8F0',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255, 255, 255, 0.04)' },
                        ticks: { color: '#94A3B8', font: { family: 'Outfit', size: 11 } }
                    },
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.04)' },
                        ticks: { color: '#94A3B8', font: { family: 'Outfit', size: 11 } },
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Metric Switcher Functions for Velocity Chart
    window.switchChartMetric = function(type) {
        if (!velocityChart) return;

        const btnWatch = document.getElementById('btnChartWatch');
        const btnSync = document.getElementById('btnChartSync');
        const btnDl = document.getElementById('btnChartDl');

        // Reset button classes
        [btnWatch, btnSync, btnDl].forEach(b => {
            b.className = 'px-3 py-1.5 rounded-lg font-bold text-xs text-slate-400 hover:text-white transition-all';
        });

        if (type === 'watch') {
            btnWatch.className = 'px-3 py-1.5 rounded-lg font-bold text-xs bg-rose-500 text-white shadow-md transition-all';
            velocityChart.data.datasets[0].label = 'Watch Minutes';
            velocityChart.data.datasets[0].data = trendWatchMins;
            velocityChart.data.datasets[0].borderColor = '#F43F5E';
            velocityChart.data.datasets[0].pointBackgroundColor = '#F43F5E';
        } else if (type === 'sync') {
            btnSync.className = 'px-3 py-1.5 rounded-lg font-bold text-xs bg-purple-500 text-white shadow-md transition-all';
            velocityChart.data.datasets[0].label = 'Checkpoints';
            velocityChart.data.datasets[0].data = trendCheckpoints;
            velocityChart.data.datasets[0].borderColor = '#A855F7';
            velocityChart.data.datasets[0].pointBackgroundColor = '#A855F7';
        } else if (type === 'dl') {
            btnDl.className = 'px-3 py-1.5 rounded-lg font-bold text-xs bg-orange-500 text-white shadow-md transition-all';
            velocityChart.data.datasets[0].label = 'Downloads';
            velocityChart.data.datasets[0].data = trendDownloads;
            velocityChart.data.datasets[0].borderColor = '#F97316';
            velocityChart.data.datasets[0].pointBackgroundColor = '#F97316';
        }
        velocityChart.update();
    };

    // 3. Platform Donut Chart Setup
    const ctxPlatform = document.getElementById('platformDonutChart');
    if (ctxPlatform && typeof Chart !== 'undefined') {
        const androidVal = platformData.android || 0;
        const iosVal = platformData.ios || 0;
        const otherVal = platformData.other || 0;

        new Chart(ctxPlatform, {
            type: 'doughnut',
            data: {
                labels: ['Android', 'iOS', 'Other'],
                datasets: [{
                    data: [androidVal || 1, iosVal, otherVal],
                    backgroundColor: ['#10B981', '#38BDF8', '#A855F7'],
                    borderColor: '#12141F',
                    borderWidth: 3,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 17, 26, 0.95)',
                        titleColor: '#FFFFFF',
                        bodyColor: '#E2E8F0',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 10,
                    }
                }
            }
        });
    }

    // 4. Media Leaderboard Tab Switching
    window.switchMediaTab = function(tab) {
        const tabMovies = document.getElementById('mediaTabMovies');
        const tabSeries = document.getElementById('mediaTabSeries');
        const tabVault = document.getElementById('mediaTabVault');

        const btnMovies = document.getElementById('tabBtnMovies');
        const btnSeries = document.getElementById('tabBtnSeries');
        const btnVault = document.getElementById('tabBtnVault');

        tabMovies.classList.add('hidden');
        tabSeries.classList.add('hidden');
        tabVault.classList.add('hidden');

        [btnMovies, btnSeries, btnVault].forEach(b => {
            b.className = 'px-3.5 py-1.5 rounded-xl font-bold text-slate-400 hover:text-white transition-all';
        });

        if (tab === 'movies') {
            tabMovies.classList.remove('hidden');
            btnMovies.className = 'px-3.5 py-1.5 rounded-xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md transition-all';
        } else if (tab === 'series') {
            tabSeries.classList.remove('hidden');
            btnSeries.className = 'px-3.5 py-1.5 rounded-xl font-bold bg-gradient-to-r from-sky-600 to-blue-600 text-white shadow-md transition-all';
        } else if (tab === 'vault') {
            tabVault.classList.remove('hidden');
            btnVault.className = 'px-3.5 py-1.5 rounded-xl font-bold bg-gradient-to-r from-orange-600 to-amber-600 text-white shadow-md transition-all';
        }
    };
});
</script>
@endsection
