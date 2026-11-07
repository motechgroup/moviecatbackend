@extends('layouts.app', ['title' => 'Search Analytics & Content Discovery'])

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- HERO HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-6 sm:p-8 rounded-3xl glass-panel relative overflow-hidden border border-white/10 shadow-2xl">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-gradient-to-br from-cyan-500/10 via-teal-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>

        <div class="space-y-2 relative z-10">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-cyan-500/15 text-cyan-400 border border-cyan-500/30">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    Live User Search Stream
                </span>
                <span class="text-xs text-slate-400 font-mono">• Telemetry & Discovery</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <span>Search Intelligence & Media Intent</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 max-w-2xl leading-relaxed">
                See exact search terms entered by mobile users, conversion to movie/series views, and which TV shows or movies were discovered from user queries.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 relative z-10">
            <div class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-xs text-slate-300">
                <span class="text-slate-400">Conversion Rate:</span> <strong class="text-emerald-400">{{ $stats['conversionRate'] }}%</strong>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-bold transition-all">
                &larr; Executive Dashboard
            </a>
        </div>
    </div>

    <!-- KPI CARDS (BENTO) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Searches -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Searches Logged</div>
            <div class="text-3xl font-black text-white mt-1">{{ number_format($stats['total']) }}</div>
            <div class="text-[11px] text-cyan-400 mt-1 font-mono flex items-center gap-1">
                <span>{{ number_format($stats['today']) }} searches today</span>
            </div>
        </div>

        <!-- 2. Linked Content (Converted) -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Media Clicked / Linked</div>
            <div class="text-3xl font-black text-emerald-400 mt-1">{{ number_format($stats['linked']) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">
                <span>{{ $stats['conversionRate'] }}% clicked a movie or series</span>
            </div>
        </div>

        <!-- 3. Unique Keywords -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Unique Search Terms</div>
            <div class="text-3xl font-black text-purple-400 mt-1">{{ number_format($stats['uniqueQueries']) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Different keywords typed</div>
        </div>

        <!-- 4. Today Activity -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Today\'s Velocity</div>
            <div class="text-3xl font-black text-amber-400 mt-1">{{ number_format($stats['today']) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">User searches in last 24h</div>
        </div>
    </div>

    <!-- LEADERBOARDS ROW (Trending Searches & Discovered Content) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Top Trending Search Queries -->
        <div class="p-6 rounded-3xl glass-panel space-y-4 border border-white/10 shadow-xl">
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="text-lg">🔥</span>
                    <span>Top Trending Search Queries</span>
                </h3>
                <span class="text-[10px] font-mono text-slate-400 uppercase tracking-wider">By Search Volume</span>
            </div>

            <div class="space-y-2">
                @forelse($topQueries as $idx => $tq)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-white/[0.02] border border-white/5 hover:bg-white/[0.05] transition-all">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-xs font-bold text-white">#{{ $idx + 1 }}</span>
                            <div>
                                <span class="font-bold text-sm text-cyan-300 font-mono">"{{ $tq->query }}"</span>
                                <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                    Last searched: {{ \Carbon\Carbon::parse($tq->last_searched_at)->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-cyan-500/15 text-cyan-400 border border-cyan-500/20 font-mono">
                            {{ $tq->count }} searches
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">No search queries recorded yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Most Linked / Discovered Media from Searches -->
        <div class="p-6 rounded-3xl glass-panel space-y-4 border border-white/10 shadow-xl">
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="text-lg">🎯</span>
                    <span>Most Discovered Media (Linked to Search)</span>
                </h3>
                <span class="text-[10px] font-mono text-slate-400 uppercase tracking-wider">Top Tapped Titles</span>
            </div>

            <div class="space-y-2">
                @forelse($topLinkedMedia as $idx => $lm)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-white/[0.02] border border-white/5 hover:bg-white/[0.05] transition-all">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-xs font-bold text-white">#{{ $idx + 1 }}</span>
                            @if($lm->poster)
                                <img src="{{ $lm->poster }}" class="w-8 h-11 rounded-lg object-cover bg-slate-800" alt="{{ $lm->title }}" />
                            @else
                                <div class="w-8 h-11 rounded-lg bg-slate-800 flex items-center justify-center text-[10px] text-slate-400">N/A</div>
                            @endif
                            <div class="overflow-hidden">
                                <div class="text-xs font-bold text-white truncate max-w-[180px] sm:max-w-xs">{{ $lm->title }}</div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase {{ $lm->media_type === 'tv' ? 'bg-purple-500/20 text-purple-300' : 'bg-sky-500/20 text-sky-300' }}">
                                        {{ $lm->media_type === 'tv' ? 'TV Series' : 'Movie' }}
                                    </span>
                                    @if($lm->year)
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $lm->year }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/20 font-mono">
                            {{ $lm->count }} clicks
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">No linked search media recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- SEARCH & FILTER BAR -->
    <div class="p-4 rounded-2xl glass-panel flex flex-col md:flex-row items-center justify-between gap-4 border border-white/5">
        <form method="GET" action="{{ route('admin.searches.index') }}" class="w-full flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search queries, linked movie/series title, device ID, or username..."
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-xs text-white placeholder-slate-400 focus:outline-none focus:border-cyan-500"
                />
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select name="type" class="bg-slate-900 border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none">
                    <option value="">All Types</option>
                    <option value="movie" {{ request('type') === 'movie' ? 'selected' : '' }}>Movies</option>
                    <option value="tv" {{ request('type') === 'tv' ? 'selected' : '' }}>TV Series</option>
                </select>

                <select name="linked" class="bg-slate-900 border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none">
                    <option value="">All Searches</option>
                    <option value="yes" {{ request('linked') === 'yes' ? 'selected' : '' }}>Linked to Title</option>
                    <option value="no" {{ request('linked') === 'no' ? 'selected' : '' }}>Unlinked Only</option>
                </select>

                <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold transition-all">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- LIVE SEARCH STREAM TABLE -->
    <div class="glass-panel rounded-3xl overflow-hidden border border-white/10 shadow-2xl">
        <div class="p-5 border-b border-white/5 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span>Live User Search Log & Linked Media Stream</span>
            </h3>
            <span class="text-xs text-slate-400 font-mono">{{ $searches->total() }} total entries</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-white/[0.02] text-slate-400 font-mono text-[10px] uppercase border-b border-white/5">
                    <tr>
                        <th class="py-3 px-4">Search Term</th>
                        <th class="py-3 px-4">Linked Title / Discovery</th>
                        <th class="py-3 px-4">User & Device</th>
                        <th class="py-3 px-4 text-center">Results</th>
                        <th class="py-3 px-4 text-right">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-300">
                    @forelse($searches as $item)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <!-- Search Term -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-xl bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-bold font-mono text-xs">
                                        {{ $item->query }}
                                    </span>
                                </div>
                            </td>

                            <!-- Linked Title -->
                            <td class="py-3.5 px-4">
                                @if($item->title)
                                    <div class="flex items-center gap-2.5">
                                        @if($item->poster)
                                            <img src="{{ $item->poster }}" class="w-7 h-9 rounded object-cover bg-slate-800" alt="{{ $item->title }}" />
                                        @else
                                            <div class="w-7 h-9 rounded bg-slate-800 flex items-center justify-center text-[9px] text-slate-500">N/A</div>
                                        @endif
                                        <div>
                                            <div class="font-bold text-white">{{ $item->title }}</div>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase {{ $item->media_type === 'tv' ? 'bg-purple-500/20 text-purple-300' : 'bg-sky-500/20 text-sky-300' }}">
                                                    {{ $item->media_type === 'tv' ? 'TV' : 'Movie' }}
                                                </span>
                                                @if($item->year)
                                                    <span class="text-[10px] text-slate-400 font-mono">{{ $item->year }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-500 font-mono text-[11px] italic">No title clicked</span>
                                @endif
                            </td>

                            <!-- User & Device -->
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-white">
                                    {{ $item->username ?: ($item->device->username ?? 'Guest User') }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono truncate max-w-[130px]" title="{{ $item->device_id }}">
                                    {{ $item->device_id ?: 'Unknown Device' }}
                                </div>
                            </td>

                            <!-- Results count -->
                            <td class="py-3.5 px-4 text-center font-mono text-[11px]">
                                <span class="px-2 py-0.5 rounded bg-white/5 text-slate-300 font-bold">
                                    {{ $item->results_count }}
                                </span>
                            </td>

                            <!-- Timestamp -->
                            <td class="py-3.5 px-4 text-right font-mono text-[11px] text-slate-400">
                                <div>{{ $item->created_at->format('d M Y, H:i') }}</div>
                                <div class="text-[10px] text-slate-500">{{ $item->created_at->diffForHumans() }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                No user searches found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($searches->hasPages())
            <div class="p-4 border-t border-white/5">
                {{ $searches->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
