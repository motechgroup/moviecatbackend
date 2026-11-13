@extends('layouts.app', ['title' => 'Discovered Titles Search Analytics'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- HERO HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 p-6 sm:p-8 rounded-3xl glass-panel relative overflow-hidden border border-white/10 shadow-2xl">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-gradient-to-br from-amber-500/10 via-rose-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>

        <div class="space-y-2 relative z-10">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    Discovered Titles Only
                </span>
                <span class="text-xs text-slate-400 font-mono">• Raw Keystrokes Excluded</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <span>Discovered Titles Search Metrics</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 max-w-2xl leading-relaxed">
                Metrics showing strictly the actual movies and TV series titles searched and discovered by mobile users. Raw letters and unlinked search fragments are filtered out.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 relative z-10">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-slate-300 text-xs font-bold transition-all flex items-center gap-2">
                <span>&larr;</span>
                <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- KPI METRICS ROW -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- 1. Total Title Searches -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Title Searches</div>
            <div class="text-3xl font-black text-white mt-1">{{ number_format($stats['total']) }}</div>
            <div class="text-[11px] text-emerald-400 mt-2 font-mono flex items-center gap-1">
                <span>+{{ number_format($stats['today']) }} today</span>
            </div>
        </div>

        <!-- 2. Unique Titles -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Unique Titles Discovered</div>
            <div class="text-3xl font-black text-amber-400 mt-1">{{ number_format($stats['uniqueTitles']) }}</div>
            <div class="text-[11px] text-slate-400 mt-2 font-mono">
                <span>Distinct movie/TV titles</span>
            </div>
        </div>

        <!-- 3. Top Searched Title -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between col-span-2 lg:col-span-1">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">#1 Most Searched Title</div>
            <div class="text-lg font-black text-white mt-1 truncate" title="{{ $stats['topTitle'] }}">
                {{ $stats['topTitle'] }}
            </div>
            <div class="text-[11px] text-amber-300 mt-2 font-mono">
                <span>{{ number_format($stats['topTitleCount']) }} searches</span>
            </div>
        </div>

        <!-- 4. Content Demand Mix -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Format Demand Split</div>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-xs font-bold text-sky-400">Movies {{ $stats['moviePercentage'] }}%</span>
                <span class="text-slate-600">/</span>
                <span class="text-xs font-bold text-purple-400">TV {{ $stats['tvPercentage'] }}%</span>
            </div>
            <div class="w-full bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden flex">
                <div class="bg-sky-400 h-1.5" style="width: {{ $stats['moviePercentage'] }}%"></div>
                <div class="bg-purple-400 h-1.5" style="width: {{ $stats['tvPercentage'] }}%"></div>
            </div>
        </div>

        <!-- 5. Unique Devices -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Audience Reach</div>
            <div class="text-3xl font-black text-cyan-400 mt-1">{{ number_format($stats['uniqueDevices']) }}</div>
            <div class="text-[11px] text-slate-400 mt-2 font-mono">
                <span>Unique mobile devices</span>
            </div>
        </div>
    </div>

    <!-- TOP SEARCHED TITLES LEADERBOARD CARDS -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-white flex items-center gap-2">
                <span class="text-xl">🏆</span>
                <span>Top Discovered Titles</span>
            </h2>
            <span class="text-xs font-mono text-slate-400">Ranked by user search volume</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @forelse($topShowcase as $index => $item)
                <div class="glass-panel rounded-2xl overflow-hidden border border-white/10 hover:border-amber-500/40 transition-all flex flex-col justify-between group">
                    <div class="relative aspect-[2/3] w-full bg-slate-900 overflow-hidden">
                        @if($item->poster)
                            <img src="{{ $item->poster }}" alt="{{ $item->display_title }}" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300" />
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
                        <div class="text-xs font-bold text-white truncate" title="{{ $item->display_title }}">
                            {{ $item->display_title }}
                        </div>
                        <div class="flex items-center justify-between text-[11px] font-mono">
                            <span class="text-amber-400 font-bold">{{ $item->search_count }} searches</span>
                            <span class="text-slate-500">{{ $item->year ?: '' }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-xs text-slate-500">
                    No discovered title searches recorded yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="p-4 sm:p-5 rounded-2xl glass-panel border border-white/5 space-y-4">
        <form method="GET" action="{{ route('admin.searches.index') }}" class="flex flex-col lg:flex-row items-center justify-between gap-4">
            <!-- Keyword search input -->
            <div class="relative w-full lg:max-w-md">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search by title name, actor, or user..."
                    class="w-full bg-[#12131A] border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-400"
                />
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                <select name="type" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-amber-400">
                    <option value="">All Formats (Movies & Series)</option>
                    <option value="movie" {{ request('type') === 'movie' ? 'selected' : '' }}>Movies Only</option>
                    <option value="tv" {{ request('type') === 'tv' ? 'selected' : '' }}>TV Series Only</option>
                </select>

                <select name="period" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-amber-400">
                    <option value="all" {{ request('period') === 'all' || !request('period') ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Today (Last 24h)</option>
                    <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Last 30 Days</option>
                </select>

                <select name="sort" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-amber-400">
                    <option value="count" {{ request('sort') === 'count' || !request('sort') ? 'selected' : '' }}>Sort: Most Searched</option>
                    <option value="recent" {{ request('sort') === 'recent' ? 'selected' : '' }}>Sort: Most Recent</option>
                    <option value="reach" {{ request('sort') === 'reach' ? 'selected' : '' }}>Sort: Unique Audience</option>
                </select>

                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-bold transition-all shadow-md shadow-amber-500/20">
                    Apply Filters
                </button>

                @if(request('q') || request('type') || request('period') || request('sort'))
                    <a href="{{ route('admin.searches.index') }}" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 text-xs font-semibold transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- DETAILED TITLES METRICS TABLE -->
    <div class="glass-panel rounded-3xl overflow-hidden border border-white/10 shadow-2xl">
        <div class="p-5 border-b border-white/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Discovered Titles Demand Metrics</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Showing actual movie and TV titles discovered through user search intent.</p>
            </div>
            <div class="text-xs font-mono text-slate-400">
                <span>{{ $titles->total() }} titles found</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-white/[0.02] text-slate-400 font-mono text-[10px] uppercase border-b border-white/5">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4 min-w-[240px]">Discovered Title</th>
                        <th class="py-3 px-4 text-center">Search Volume</th>
                        <th class="py-3 px-4 text-center">Audience Reach</th>
                        <th class="py-3 px-4 min-w-[200px]">Search Queries Used</th>
                        <th class="py-3 px-4 text-right">Last Searched</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-300">
                    @forelse($titles as $idx => $item)
                        @php
                            $rank = ($titles->currentPage() - 1) * $titles->perPage() + $idx + 1;
                            $percent = $stats['total'] > 0 ? round(($item->search_count / $stats['total']) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-white/[0.03] transition-colors">
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-400">
                                {{ $rank }}
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    @if($item->poster)
                                        <img src="{{ $item->poster }}" class="w-10 h-14 rounded-lg object-cover bg-slate-800 flex-shrink-0 border border-white/10" alt="{{ $item->display_title }}" />
                                    @else
                                        <div class="w-10 h-14 rounded-lg bg-slate-800 flex-shrink-0 flex items-center justify-center text-[10px] text-slate-500 border border-white/5">
                                            🎬
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-extrabold text-sm text-white hover:text-amber-400 transition-colors">
                                            {{ $item->display_title }}
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $item->media_type === 'tv' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-sky-500/20 text-sky-300 border border-sky-500/30' }}">
                                                {{ $item->media_type === 'tv' ? 'TV Series' : 'Movie' }}
                                            </span>
                                            @if($item->year)
                                                <span class="text-[11px] text-slate-400 font-mono">{{ $item->year }}</span>
                                            @endif
                                            @if($item->media_id)
                                                <span class="text-[10px] text-slate-500 font-mono">ID: {{ $item->media_id }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-amber-500/15 text-amber-300 border border-amber-500/30 font-bold font-mono text-xs">
                                    {{ number_format($item->search_count) }} searches
                                </div>
                                <div class="text-[10px] text-slate-500 font-mono mt-1">
                                    {{ $percent }}% of total demand
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <div class="text-xs font-bold text-white font-mono">
                                    {{ $item->unique_devices }} {{ Str::plural('device', $item->unique_devices) }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                    {{ $item->unique_users }} unique {{ Str::plural('user', $item->unique_users) }}
                                </div>
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="flex flex-wrap gap-1.5 max-w-xs">
                                    @if($item->sample_queries)
                                        @foreach(array_slice(explode(' • ', $item->sample_queries), 0, 4) as $queryWord)
                                            <span class="px-2 py-0.5 rounded-lg bg-white/5 border border-white/10 text-slate-300 font-mono text-[11px]">
                                                "{{ $queryWord }}"
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-slate-500 font-mono text-[11px] italic">Direct match</span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-right font-mono text-[11px] text-slate-400">
                                <div class="text-white font-semibold">
                                    {{ \Carbon\Carbon::parse($item->last_searched_at)->diffForHumans() }}
                                </div>
                                <div class="text-[10px] text-slate-500">
                                    {{ \Carbon\Carbon::parse($item->last_searched_at)->format('d M Y, H:i') }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-14 text-center text-slate-400">
                                No discovered titles found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($titles->hasPages())
            <div class="p-4 border-t border-white/5">
                {{ $titles->links() }}
            </div>
        @endif
    </div>

    <!-- LIVE USER TITLE SEARCH STREAM -->
    <div class="glass-panel rounded-3xl p-6 border border-white/10 space-y-4">
        <div class="flex items-center justify-between border-b border-white/5 pb-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>Real-Time Discovered Title Activity</span>
            </h3>
            <span class="text-xs text-slate-500 font-mono">Last 10 discovered titles</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @forelse($recentStream as $streamItem)
                <div class="p-3.5 rounded-2xl bg-[#12131A] border border-white/5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 overflow-hidden">
                        @if($streamItem->poster)
                            <img src="{{ $streamItem->poster }}" class="w-8 h-11 rounded-lg object-cover bg-slate-800 flex-shrink-0" alt="{{ $streamItem->title }}" />
                        @else
                            <div class="w-8 h-11 rounded-lg bg-slate-800 flex-shrink-0 flex items-center justify-center text-[10px] text-slate-500">🎬</div>
                        @endif
                        <div class="overflow-hidden">
                            <div class="text-xs font-bold text-white truncate">
                                {{ $streamItem->title }}
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5 truncate">
                                Searched: "<span class="text-amber-300">{{ $streamItem->query }}</span>" • By {{ $streamItem->username ?: ($streamItem->device->username ?? 'Guest User') }}
                            </div>
                        </div>
                    </div>
                    <div class="text-right text-[10px] font-mono text-slate-500 flex-shrink-0">
                        {{ $streamItem->created_at->diffForHumans() }}
                    </div>
                </div>
            @empty
                <div class="col-span-full py-4 text-center text-xs text-slate-500">
                    No recent discovered title events.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
