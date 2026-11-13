@extends('layouts.app', ['title' => 'Watch Progress & Completion Analytics'])

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- HERO HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 p-6 sm:p-8 rounded-3xl glass-panel relative overflow-hidden border border-white/10 shadow-2xl">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-gradient-to-br from-purple-500/10 via-pink-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>

        <div class="space-y-2 relative z-10">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-500/15 text-purple-400 border border-purple-500/30">
                    <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
                    Watch Progress & Completion Intelligence
                </span>
                <span class="text-xs text-slate-400 font-mono">• Telemetry & Audience Retention</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <span>Watch Progress & Completion Analytics</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 max-w-2xl leading-relaxed">
                Detailed telemetry of user playback progress, stream checkpoint velocities, audience completion funnels, and retention analytics comparing Cloud Streams vs Offline Local Vaults.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 relative z-10">
            <div class="px-3.5 py-2 rounded-xl bg-white/5 border border-white/10 text-xs font-mono text-slate-300 flex items-center gap-2">
                <span>⏱️ Total Playback: <strong class="text-purple-300 font-bold">{{ $stats['totalWatchTime'] }}</strong></span>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-slate-300 text-xs font-bold transition-all flex items-center gap-1.5">
                <span>&larr;</span>
                <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- PRIMARY 5-CARD KPI ROW -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- 1. Total Checkpoints -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Stream Checkpoints</div>
            <div class="text-3xl font-black text-white mt-1">{{ number_format($stats['totalCheckpoints']) }}</div>
            <div class="text-[11px] text-purple-300 mt-2 font-mono">
                <span>Across {{ number_format($stats['uniqueDevices']) }} active devices</span>
            </div>
        </div>

        <!-- 2. Overall Completion Rate -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Completion Rate</div>
            <div class="text-3xl font-black text-emerald-400 mt-1">{{ $stats['completionRate'] }}%</div>
            <div class="text-[11px] text-emerald-300 mt-2 font-mono">
                <span>{{ number_format($stats['completed']) }} finished / {{ number_format($stats['inProgress']) }} in-progress</span>
            </div>
        </div>

        <!-- 3. Average Progress % -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Average Progress</div>
            <div class="text-3xl font-black text-cyan-400 mt-1">{{ $stats['avgProgressPct'] }}%</div>
            <div class="text-[11px] text-slate-400 mt-2 font-mono">
                <span>Avg session: {{ $stats['avgSessionTime'] }}</span>
            </div>
        </div>

        <!-- 4. Playback Sources -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Offline vs Online</div>
            <div class="text-2xl sm:text-3xl font-black text-orange-400 mt-1">{{ number_format($stats['offlineWatches']) }}</div>
            <div class="text-[11px] text-slate-400 mt-2 font-mono">
                <span>Vault plays vs {{ number_format($stats['onlineWatches']) }} streams</span>
            </div>
        </div>

        <!-- 5. Format Split -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between col-span-2 lg:col-span-1">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Content Format Mix</div>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-xs font-bold text-sky-400">Movies {{ $stats['moviePct'] }}%</span>
                <span class="text-slate-600">/</span>
                <span class="text-xs font-bold text-purple-400">TV {{ $stats['tvPct'] }}%</span>
            </div>
            <div class="w-full bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden flex">
                <div class="bg-sky-400 h-1.5" style="width: {{ $stats['moviePct'] }}%"></div>
                <div class="bg-purple-400 h-1.5" style="width: {{ $stats['tvPct'] }}%"></div>
            </div>
        </div>
    </div>

    <!-- WATCH PROGRESS FUNNEL & COMPLETION METRICS MODULE -->
    <div class="p-6 sm:p-7 rounded-3xl glass-panel space-y-5 border border-white/10 shadow-2xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                    <span>📊 Playback Progress & Completion Funnel</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Audience retention drop-off from initial playback start to full completion.</p>
            </div>
            <span class="text-[10px] font-mono text-purple-400 font-bold bg-purple-500/10 px-2.5 py-1 rounded-xl border border-purple-500/20">Retention Funnel</span>
        </div>

        <!-- Progress Funnel Bar -->
        <div class="space-y-2">
            <div class="w-full bg-white/5 rounded-2xl h-4 overflow-hidden flex shadow-inner">
                <div class="bg-slate-500 h-full transition-all duration-500" style="width: {{ $stats['funnel']['started_pct'] }}%" title="Started: {{ $stats['funnel']['started_pct'] }}%"></div>
                <div class="bg-amber-500 h-full transition-all duration-500" style="width: {{ $stats['funnel']['engaged_pct'] }}%" title="Engaged: {{ $stats['funnel']['engaged_pct'] }}%"></div>
                <div class="bg-purple-500 h-full transition-all duration-500" style="width: {{ $stats['funnel']['hooked_pct'] }}%" title="Hooked: {{ $stats['funnel']['hooked_pct'] }}%"></div>
                <div class="bg-emerald-500 h-full transition-all duration-500" style="width: {{ $stats['funnel']['completed_pct'] }}%" title="Completed: {{ $stats['funnel']['completed_pct'] }}%"></div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                <!-- 0-25% -->
                <div class="p-3 rounded-2xl bg-white/[0.02] border border-white/5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                            Started (0 - 25%)
                        </span>
                        <span class="text-xs font-mono text-slate-400 font-bold">{{ $stats['funnel']['started_pct'] }}%</span>
                    </div>
                    <div class="text-base font-black text-white mt-1">{{ number_format($stats['startedCount']) }}</div>
                    <span class="text-[10px] text-slate-500">Early bounce / sample</span>
                </div>

                <!-- 25-50% -->
                <div class="p-3 rounded-2xl bg-white/[0.02] border border-white/5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-amber-400 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Engaged (25 - 50%)
                        </span>
                        <span class="text-xs font-mono text-amber-400 font-bold">{{ $stats['funnel']['engaged_pct'] }}%</span>
                    </div>
                    <div class="text-base font-black text-white mt-1">{{ number_format($stats['engagedCount']) }}</div>
                    <span class="text-[10px] text-slate-500">Watching middle act</span>
                </div>

                <!-- 50-85% -->
                <div class="p-3 rounded-2xl bg-white/[0.02] border border-white/5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-purple-400 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            Hooked (50 - 85%)
                        </span>
                        <span class="text-xs font-mono text-purple-400 font-bold">{{ $stats['funnel']['hooked_pct'] }}%</span>
                    </div>
                    <div class="text-base font-black text-white mt-1">{{ number_format($stats['hookedCount']) }}</div>
                    <span class="text-[10px] text-slate-500">Climax / high intent</span>
                </div>

                <!-- 85-100% -->
                <div class="p-3 rounded-2xl bg-white/[0.02] border border-white/5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-emerald-400 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Completed (85%+)
                        </span>
                        <span class="text-xs font-mono text-emerald-400 font-bold">{{ $stats['funnel']['completed_pct'] }}%</span>
                    </div>
                    <div class="text-base font-black text-emerald-400 mt-1">{{ number_format($stats['completed']) }}</div>
                    <span class="text-[10px] text-slate-500">Full finish / credits</span>
                </div>
            </div>
        </div>

        <!-- Comparative Completion Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-white/5">
            <!-- Format Completion Comparison -->
            <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-300 block">🎬 Movies Completion Rate</span>
                    <span class="text-[10px] text-slate-500">Full movie stream completions</span>
                </div>
                <div class="text-right">
                    <span class="text-lg font-black text-sky-400 font-mono">{{ $stats['movieCompletionRate'] }}%</span>
                    <span class="text-[10px] text-slate-400 block font-mono">vs TV: {{ $stats['tvCompletionRate'] }}%</span>
                </div>
            </div>

            <!-- Source Completion Comparison -->
            <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-300 block">💾 Offline Vault Completion Rate</span>
                    <span class="text-[10px] text-slate-500">Locally saved playback completion</span>
                </div>
                <div class="text-right">
                    <span class="text-lg font-black text-orange-400 font-mono">{{ $stats['offlineCompletionRate'] }}%</span>
                    <span class="text-[10px] text-slate-400 block font-mono">vs Online: {{ $stats['onlineCompletionRate'] }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TOP COMPLETED TITLES SHOWCASE -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-white flex items-center gap-2">
                <span class="text-xl">🏆</span>
                <span>Top Completed Titles</span>
            </h2>
            <span class="text-xs font-mono text-slate-400">Content with highest full-watch completions</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @forelse($topCompleted as $index => $item)
                <div class="glass-panel rounded-2xl overflow-hidden border border-white/10 hover:border-purple-500/40 transition-all flex flex-col justify-between group">
                    <div class="relative aspect-[2/3] w-full bg-slate-900 overflow-hidden">
                        @if($item->poster)
                            <img src="{{ $item->poster }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300" />
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-800 to-slate-900 p-3 text-center">
                                <span class="text-2xl mb-1">🎬</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Poster N/A</span>
                            </div>
                        @endif

                        <!-- Rank Badge -->
                        <div class="absolute top-2 left-2 px-2 py-0.5 rounded-lg text-xs font-black shadow-lg {{ $index === 0 ? 'bg-amber-400 text-black' : ($index === 1 ? 'bg-slate-300 text-black' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-black/80 text-white border border-white/20')) }}">
                            #{{ $index + 1 }}
                        </div>

                        <!-- Type Badge -->
                        <div class="absolute bottom-2 right-2 px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $item->media_type === 'tv' ? 'bg-purple-600/90 text-white' : 'bg-sky-600/90 text-white' }}">
                            {{ $item->media_type === 'tv' ? 'Series' : 'Movie' }}
                        </div>
                    </div>

                    <div class="p-3 space-y-1.5 bg-slate-950/40 flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-white truncate group-hover:text-purple-300 transition-colors" title="{{ $item->title }}">
                                {{ $item->title }}
                            </h4>
                            <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono mt-1">
                                <span>{{ $item->completed_count }} completed</span>
                                <span class="text-emerald-400 font-bold">{{ $item->completion_rate }}%</span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-white/5 text-[9px] font-mono text-slate-500 flex items-center justify-between">
                            <span>{{ $item->view_count }} total views</span>
                            <span class="text-purple-300">{{ $item->formatted_watch_time }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-xs text-slate-400 glass-panel rounded-2xl">
                    No completed watches captured yet. Completed titles will automatically rank here.
                </div>
            @endforelse
        </div>
    </div>

    <!-- FILTER BAR & STREAM TELEMETRY TABLE -->
    <div class="p-6 sm:p-7 rounded-3xl glass-panel space-y-5 border border-white/10 shadow-2xl">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                    <span>📡 Stream Telemetry Logs</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Real-time watch progress checkpoints from active mobile devices.</p>
            </div>

            <!-- Multi-Filter Bar -->
            <form method="GET" action="{{ route('admin.sync.index') }}" class="flex flex-wrap items-center gap-2.5">
                <!-- Search input -->
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search title, device..." 
                           class="w-48 sm:w-56 px-3.5 py-2 pl-9 rounded-xl bg-white/5 border border-white/10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-purple-500" />
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Source selector -->
                <select name="source" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-xs text-slate-300 focus:outline-none focus:border-purple-500">
                    <option value="">All Sources</option>
                    <option value="stream" {{ request('source') === 'stream' ? 'selected' : '' }}>Cloud Stream</option>
                    <option value="offline" {{ request('source') === 'offline' ? 'selected' : '' }}>Offline Vault</option>
                </select>

                <!-- Format selector -->
                <select name="media_type" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-xs text-slate-300 focus:outline-none focus:border-purple-500">
                    <option value="">All Formats</option>
                    <option value="movie" {{ request('media_type') === 'movie' ? 'selected' : '' }}>Movies</option>
                    <option value="tv" {{ request('media_type') === 'tv' ? 'selected' : '' }}>TV Series</option>
                </select>

                <!-- Completion Status selector -->
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-xs text-slate-300 focus:outline-none focus:border-purple-500">
                    <option value="">All Progress</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed (85%+)</option>
                    <option value="hooked" {{ request('status') === 'hooked' ? 'selected' : '' }}>Hooked (50-85%)</option>
                    <option value="engaged" {{ request('status') === 'engaged' ? 'selected' : '' }}>Engaged (25-50%)</option>
                    <option value="started" {{ request('status') === 'started' ? 'selected' : '' }}>Started (0-25%)</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In-Progress (<85%)</option>
                </select>

                <!-- Sort selector -->
                <select name="sort" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-xs text-slate-300 focus:outline-none focus:border-purple-500">
                    <option value="recent" {{ request('sort') === 'recent' ? 'selected' : '' }}>Recent</option>
                    <option value="progress" {{ request('sort') === 'progress' ? 'selected' : '' }}>Highest %</option>
                    <option value="duration" {{ request('sort') === 'duration' ? 'selected' : '' }}>Longest Watched</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                </select>

                <button type="submit" class="px-3 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold transition-all">Filter</button>
                @if(request()->hasAny(['search', 'source', 'media_type', 'status', 'sort']))
                    <a href="{{ route('admin.sync.index') }}" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 text-xs transition-all">Reset</a>
                @endif
            </form>
        </div>

        <!-- Telemetry Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3 px-3">Media Title</th>
                        <th class="py-3 px-3">Device / User</th>
                        <th class="py-3 px-3">Source</th>
                        <th class="py-3 px-3">Progress Gauge</th>
                        <th class="py-3 px-3">Completion Stage</th>
                        <th class="py-3 px-3 text-right">Last Watched</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-xs">
                    @forelse($history as $item)
                        @php
                            $duration = (float)($item->duration ?: 0);
                            $curr = (float)($item->current_time ?: 0);
                            $pct = $duration > 0 ? min(100, round(($curr / $duration) * 100)) : 0;
                            $isDone = $item->completed || $pct >= 85;
                        @endphp
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-3">
                                    @if($item->poster)
                                        <img src="{{ $item->poster }}" class="w-9 h-12 rounded-lg object-cover bg-slate-800 shrink-0" alt="{{ $item->title }}" />
                                    @else
                                        <div class="w-9 h-12 rounded-lg bg-slate-800 flex items-center justify-center text-[10px] text-slate-500 shrink-0">N/A</div>
                                    @endif
                                    <div class="overflow-hidden">
                                        <div class="font-bold text-white truncate max-w-xs">{{ $item->title }}</div>
                                        <div class="flex items-center gap-2 mt-0.5 text-[10px]">
                                            <span class="px-1.5 py-0.2 rounded font-bold uppercase {{ $item->media_type === 'tv' ? 'bg-purple-500/15 text-purple-400' : 'bg-sky-500/15 text-sky-400' }}">
                                                {{ $item->media_type === 'tv' ? 'TV' : 'Movie' }}
                                            </span>
                                            @if($item->season && $item->episode)
                                                <span class="text-slate-400 font-mono">S{{ $item->season }}E{{ $item->episode }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <a href="{{ route('admin.devices.show', $item->device_id) }}" class="font-bold text-white hover:text-purple-400 transition-colors block">
                                    {{ $item->device->display_name ?? 'Guest User' }}
                                </a>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $item->device->country_display ?? '🌐 Global' }}</span>
                            </td>
                            <td class="py-3 px-3">
                                @if($item->is_offline)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-500/15 text-orange-400 border border-orange-500/30 font-mono">
                                        <span>💾</span> Offline Vault
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-500/15 text-sky-400 border border-sky-500/30 font-mono">
                                        <span>🌐</span> Cloud Stream
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <div class="w-40 space-y-1">
                                    <div class="flex items-center justify-between text-[10px] font-mono">
                                        <span class="text-slate-300 font-bold">{{ $pct }}%</span>
                                        <span class="text-slate-500">{{ round($curr / 60) }}m / {{ round($duration / 60) }}m</span>
                                    </div>
                                    <div class="w-full bg-white/5 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-300 {{ $isDone ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-purple-500' : ($pct >= 25 ? 'bg-amber-500' : 'bg-slate-500')) }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                @if($isDone)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                        <span>✓</span> Completed
                                    </span>
                                @elseif($pct >= 50)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-500/15 text-purple-300 border border-purple-500/30">
                                        Hooked ({{ $pct }}%)
                                    </span>
                                @elseif($pct >= 25)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                        Engaged ({{ $pct }}%)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-500/15 text-slate-400 border border-slate-500/30">
                                        Started ({{ $pct }}%)
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right">
                                <span class="text-slate-400 font-mono text-[11px]">
                                    {{ $item->last_watched_at ? $item->last_watched_at->diffForHumans() : 'Recently' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-xs text-slate-400">
                                No playback telemetry records match your active filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($history->hasPages())
            <div class="pt-4 border-t border-white/5">
                {{ $history->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
