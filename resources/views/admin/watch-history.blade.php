@extends('layouts.app', ['title' => 'Watch Progress Stream'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- HERO HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 p-6 sm:p-8 rounded-3xl glass-panel relative overflow-hidden border border-white/10 shadow-2xl">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-gradient-to-br from-purple-500/10 via-pink-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>

        <div class="space-y-2 relative z-10">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-500/15 text-purple-400 border border-purple-500/30">
                    <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
                    Viewing Telemetry Stream
                </span>
                <span class="text-xs text-slate-400 font-mono">• Online Streams & Offline Watched</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <span>Watch History & Stream Analytics</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 max-w-2xl leading-relaxed">
                Detailed telemetry of user viewing sessions, playback checkpoints, completed watches, and breakdown between online CDN streams and offline vault playback.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 relative z-10">
            <div class="px-4 py-2.5 rounded-2xl bg-white/5 border border-white/10 text-xs font-mono text-slate-300 flex items-center gap-2">
                <span class="text-base">⏱️</span>
                <span>Total Watched: <strong class="text-white font-bold">{{ $stats['totalWatchTime'] }}</strong></span>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-slate-300 text-xs font-bold transition-all flex items-center gap-2">
                <span>&larr;</span>
                <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- KPI METRICS ROW -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- 1. Total Checkpoints -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Checkpoints</div>
            <div class="text-3xl font-black text-white mt-1">{{ number_format($stats['totalCheckpoints']) }}</div>
            <div class="text-[11px] text-purple-300 mt-2 font-mono">
                <span>Across {{ number_format($stats['uniqueDevices']) }} devices</span>
            </div>
        </div>

        <!-- 2. Finished Streams -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Finished Watches</div>
            <div class="text-3xl font-black text-emerald-400 mt-1">{{ number_format($stats['completed']) }}</div>
            <div class="text-[11px] text-emerald-300 mt-2 font-mono">
                <span>{{ $stats['completionRate'] }}% completion rate</span>
            </div>
        </div>

        <!-- 3. Offline Watched from Vault -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Offline Vault Watched</div>
            <div class="text-3xl font-black text-orange-400 mt-1">{{ number_format($stats['offlineWatches']) }}</div>
            <div class="text-[11px] text-slate-400 mt-2 font-mono">
                <span>vs {{ number_format($stats['onlineWatches']) }} online streams</span>
            </div>
        </div>

        <!-- 4. Accumulated Watch Time -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Playback Time</div>
            <div class="text-2xl sm:text-3xl font-black text-cyan-400 mt-1 truncate">{{ $stats['totalWatchTime'] }}</div>
            <div class="text-[11px] text-slate-400 mt-2 font-mono">
                <span>Total consumed hours</span>
            </div>
        </div>

        <!-- 5. Content Format Mix -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between col-span-2 lg:col-span-1">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Format Demand Split</div>
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

    <!-- TOP VIEWED TITLES SHOWCASE -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-white flex items-center gap-2">
                <span class="text-xl">🏆</span>
                <span>Top Viewed Titles</span>
            </h2>
            <span class="text-xs font-mono text-slate-400">Ranked by audience watch sessions</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @forelse($topViewed as $index => $item)
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
                            {{ $item->media_type === 'tv' ? 'TV' : 'Movie' }}
                        </div>
                    </div>

                    <div class="p-3 space-y-1.5">
                        <div class="text-xs font-bold text-white truncate" title="{{ $item->title }}">
                            {{ $item->title }}
                        </div>
                        <div class="flex items-center justify-between text-[11px] font-mono">
                            <span class="text-purple-400 font-bold">{{ $item->view_count }} views</span>
                            <span class="text-slate-500">{{ $item->formatted_watch_time }}</span>
                        </div>
                        @if($item->offline_count > 0)
                            <div class="text-[10px] font-mono text-orange-400 flex items-center gap-1">
                                <span>📦</span>
                                <span>{{ $item->offline_count }} offline watches</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-xs text-slate-500">
                    No viewing checkpoints recorded yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- SEARCH & FILTER BAR -->
    <div class="glass-panel p-4 rounded-2xl border border-white/10 shadow-xl">
        <form method="GET" action="{{ route('admin.sync.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <!-- Search Input -->
            <div class="flex-1 min-w-[240px]">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by movie / series title, device ID, or username..."
                    class="w-full bg-[#12131A] border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-purple-400"
                />
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                <select name="type" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-purple-400">
                    <option value="">All Formats</option>
                    <option value="movie" {{ request('type') === 'movie' ? 'selected' : '' }}>Movies Only</option>
                    <option value="tv" {{ request('type') === 'tv' ? 'selected' : '' }}>TV Series Only</option>
                </select>

                <select name="source" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-purple-400">
                    <option value="">All Viewing Sources</option>
                    <option value="offline" {{ request('source') === 'offline' ? 'selected' : '' }}>📦 Offline Vault Only</option>
                    <option value="online" {{ request('source') === 'online' ? 'selected' : '' }}>🌐 Cloud Stream Only</option>
                </select>

                <select name="status" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-purple-400">
                    <option value="">All Progress Statuses</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Finished / Completed Only</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress Only</option>
                </select>

                <select name="period" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-purple-400">
                    <option value="" {{ !request('period') ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Today (Last 24h)</option>
                    <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Last 30 Days</option>
                </select>

                <select name="sort" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-purple-400">
                    <option value="recent" {{ request('sort') === 'recent' || !request('sort') ? 'selected' : '' }}>Sort: Most Recent</option>
                    <option value="progress" {{ request('sort') === 'progress' ? 'selected' : '' }}>Sort: Longest Progress</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Sort: Oldest</option>
                </select>

                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold transition-all shadow-md shadow-purple-600/20">
                    Apply Filters
                </button>

                @if(request('search') || request('type') || request('source') || request('status') || request('period') || request('sort'))
                    <a href="{{ route('admin.sync.index') }}" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 text-xs font-semibold transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- DETAILED WATCH HISTORY TELEMETRY TABLE -->
    <div class="glass-panel rounded-3xl overflow-hidden border border-white/10 shadow-2xl">
        <div class="p-5 border-b border-white/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Watch Progress Checkpoints Stream</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Real-time telemetry showing live checkpoints, completion rates, and viewing source.</p>
            </div>
            <div class="text-xs font-mono text-slate-400">
                <span>{{ $history->total() }} checkpoints found</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-white/[0.02] text-slate-400 font-mono text-[10px] uppercase border-b border-white/5">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4 min-w-[240px]">Media Title</th>
                        <th class="py-3 px-4 text-center">Viewing Source</th>
                        <th class="py-3 px-4 min-w-[200px]">Playback Progress</th>
                        <th class="py-3 px-4 min-w-[180px]">User & Device</th>
                        <th class="py-3 px-4 text-right">Last Watched</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-300">
                    @forelse($history as $idx => $item)
                        @php
                            $rank = ($history->currentPage() - 1) * $history->perPage() + $idx + 1;
                            $pct = $item->duration > 0 ? min(100, round(($item->current_time / $item->duration) * 100)) : 0;
                            $curM = floor($item->current_time / 60);
                            $durM = floor($item->duration / 60);
                        @endphp
                        <tr class="hover:bg-white/[0.03] transition-colors">
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-500">
                                {{ $rank }}
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    @if($item->poster)
                                        <img src="{{ $item->poster }}" class="w-10 h-14 rounded-lg object-cover bg-slate-800 flex-shrink-0 border border-white/10" alt="{{ $item->title }}" />
                                    @else
                                        <div class="w-10 h-14 rounded-lg bg-slate-800 flex-shrink-0 flex items-center justify-center text-[10px] text-slate-500 border border-white/5">
                                            🎬
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-extrabold text-sm text-white hover:text-purple-400 transition-colors">
                                            {{ $item->title }}
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $item->media_type === 'tv' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-sky-500/20 text-sky-300 border border-sky-500/30' }}">
                                                {{ $item->media_type === 'tv' ? 'TV Series' : 'Movie' }}
                                            </span>
                                            @if($item->season && $item->episode)
                                                <span class="text-[11px] text-amber-400 font-mono font-bold">S{{ $item->season }}:E{{ $item->episode }}</span>
                                            @endif
                                            @if($item->media_id)
                                                <span class="text-[10px] text-slate-500 font-mono">ID: {{ $item->media_id }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                @if($item->is_offline)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-orange-500/15 text-orange-400 border border-orange-500/30">
                                        <span>📦</span>
                                        <span>Vault Offline</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-500/15 text-sky-400 border border-sky-500/30">
                                        <span>🌐</span>
                                        <span>Cloud Stream</span>
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="space-y-1.5 max-w-[220px]">
                                    <div class="flex items-center justify-between text-[11px] font-mono">
                                        <span class="text-white font-bold">{{ $curM }}m / {{ $durM }}m</span>
                                        @if($item->completed || $pct >= 95)
                                            <span class="text-emerald-400 font-bold flex items-center gap-1">
                                                <span>✓</span> Finished
                                            </span>
                                        @else
                                            <span class="text-slate-400">{{ $pct }}%</span>
                                        @endif
                                    </div>
                                    <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                        <div class="{{ $item->completed || $pct >= 95 ? 'bg-emerald-400' : ($item->is_offline ? 'bg-orange-400' : 'bg-purple-500') }} h-1.5 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4">
                                <div>
                                    <div class="font-semibold text-xs text-slate-200">
                                        {{ $item->device->username ?? 'MovieCat User' }}
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-0.5 font-mono text-[10px] text-slate-500">
                                        <span>{{ substr($item->device_id, 0, 14) }}...</span>
                                        @if($item->device && $item->device->platform)
                                            <span class="px-1 py-0.2 rounded bg-white/5 text-slate-400 uppercase text-[9px]">{{ $item->device->platform }}</span>
                                        @endif
                                        @if($item->country_code)
                                            <span class="text-slate-400 font-bold">{{ $item->country_code }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-right font-mono text-[11px] text-slate-400">
                                <div class="text-white font-semibold">
                                    {{ $item->last_watched_at ? $item->last_watched_at->diffForHumans() : ($item->updated_at ? $item->updated_at->diffForHumans() : 'N/A') }}
                                </div>
                                <div class="text-[10px] text-slate-500">
                                    {{ $item->last_watched_at ? $item->last_watched_at->format('d M Y, H:i') : ($item->updated_at ? $item->updated_at->format('d M Y, H:i') : '') }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-14 text-center text-slate-400">
                                No viewing checkpoints found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($history->hasPages())
            <div class="p-4 border-t border-white/5">
                {{ $history->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
