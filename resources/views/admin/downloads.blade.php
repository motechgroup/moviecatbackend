@extends('layouts.app', ['title' => 'Downloads & Offline Tracking'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- HERO HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 p-6 sm:p-8 rounded-3xl glass-panel relative overflow-hidden border border-white/10 shadow-2xl">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-gradient-to-br from-orange-500/10 via-amber-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>

        <div class="space-y-2 relative z-10">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-orange-500/15 text-orange-400 border border-orange-500/30">
                    <span class="w-2 h-2 rounded-full bg-orange-400 animate-pulse"></span>
                    Offline Vault Telemetry
                </span>
                <span class="text-xs text-slate-400 font-mono">• Storage & Consumption Analytics</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <span>Downloads & Offline Activity</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 max-w-2xl leading-relaxed">
                Detailed telemetry on user offline downloads, local device storage footprints, and actual offline playback sessions across your mobile fleet.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 relative z-10">
            <div class="px-4 py-2.5 rounded-2xl bg-white/5 border border-white/10 text-xs font-mono text-slate-300 flex items-center gap-2">
                <span class="text-base">💾</span>
                <span>Total Stored: <strong class="text-white font-bold">{{ $stats['totalData'] }}</strong></span>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-slate-300 text-xs font-bold transition-all flex items-center gap-2">
                <span>&larr;</span>
                <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- MASTER DOWNLOAD SWITCH BANNER -->
    <div class="p-6 rounded-3xl glass-panel relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-4 border {{ $downloadsEnabled ? 'border-emerald-500/30 bg-emerald-950/10' : 'border-rose-500/30 bg-rose-950/10' }} shadow-xl">
        <div class="space-y-1">
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full {{ $downloadsEnabled ? 'bg-emerald-400 animate-pulse' : 'bg-rose-500' }}"></span>
                <h2 class="text-base sm:text-lg font-extrabold text-white">Downloads Master Switch: {{ $downloadsEnabled ? 'ENABLED' : 'DISABLED' }}</h2>
            </div>
            <p class="text-xs text-slate-400 max-w-xl leading-relaxed">
                When turned <strong class="text-white">{{ $downloadsEnabled ? 'OFF' : 'ON' }}</strong>, mobile clients immediately receive <code>features.downloads = {{ $downloadsEnabled ? 'false' : 'true' }}</code> via remote config. Download buttons and vault actions are {{ $downloadsEnabled ? 'silenced' : 'restored' }} in real-time.
            </p>
        </div>
        <form action="{{ route('admin.downloads.toggle') }}" method="POST">
            @csrf
            @if($downloadsEnabled)
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-rose-500/15 text-rose-400 hover:bg-rose-500/25 border border-rose-500/30 font-bold text-xs transition-all flex items-center gap-2 shadow-lg shadow-rose-500/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    Disable Downloads
                </button>
            @else
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-emerald-500/15 text-emerald-400 hover:bg-emerald-500/25 border border-emerald-500/30 font-bold text-xs transition-all flex items-center gap-2 shadow-lg shadow-emerald-500/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Enable Downloads
                </button>
            @endif
        </form>
    </div>

    <!-- KPI METRICS ROW -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- 1. Total Downloads Stored -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Vault Files Stored</div>
            <div class="text-3xl font-black text-white mt-1">{{ number_format($stats['completed']) }}</div>
            <div class="text-[11px] text-amber-400 mt-2 font-mono flex items-center gap-1">
                <span>{{ number_format($stats['activeDownloading']) }} downloading</span>
            </div>
        </div>

        <!-- 2. Offline Watched Sessions -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Offline Watched</div>
            <div class="text-3xl font-black text-emerald-400 mt-1">{{ number_format($stats['offlineWatched']) }}</div>
            <div class="text-[11px] text-emerald-300 mt-2 font-mono">
                <span>{{ $stats['offlineConversionRate'] }}% watched offline</span>
            </div>
        </div>

        <!-- 3. Total Storage Consumed -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Storage Footprint</div>
            <div class="text-3xl font-black text-sky-400 mt-1">{{ $stats['totalData'] }}</div>
            <div class="text-[11px] text-slate-400 mt-2 font-mono">
                <span>Cached on user devices</span>
            </div>
        </div>

        <!-- 4. Device Vaults -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Device Vaults</div>
            <div class="text-3xl font-black text-purple-400 mt-1">{{ number_format($stats['uniqueDevices']) }}</div>
            <div class="text-[11px] text-slate-400 mt-2 font-mono">
                <span>Unique mobile devices</span>
            </div>
        </div>

        <!-- 5. Content Format Mix -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/5 flex flex-col justify-between col-span-2 lg:col-span-1">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Format Distribution</div>
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

    <!-- TOP DOWNLOADED & SAVED TITLES SHOWCASE -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-white flex items-center gap-2">
                <span class="text-xl">🏆</span>
                <span>Top Downloaded & Stored Titles</span>
            </h2>
            <span class="text-xs font-mono text-slate-400">Ranked by total device saves</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @forelse($topTitles as $index => $item)
                <div class="glass-panel rounded-2xl overflow-hidden border border-white/10 hover:border-orange-500/40 transition-all flex flex-col justify-between group">
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
                            <span class="text-orange-400 font-bold">{{ $item->download_count }} saves</span>
                            <span class="text-slate-500">{{ $item->formatted_size }}</span>
                        </div>
                        <div class="text-[10px] font-mono text-emerald-400 flex items-center gap-1">
                            <span>▶</span>
                            <span>{{ $item->offline_watched_count }} watched offline</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-xs text-slate-500">
                    No offline downloads recorded yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- SEARCH & FILTER BAR -->
    <div class="glass-panel p-4 rounded-2xl border border-white/10 shadow-xl">
        <form method="GET" action="{{ route('admin.downloads.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <!-- Search Input -->
            <div class="flex-1 min-w-[240px]">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by title, device ID, or username..."
                    class="w-full bg-[#12131A] border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-orange-400"
                />
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                <select name="type" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-orange-400">
                    <option value="">All Formats</option>
                    <option value="movie" {{ request('type') === 'movie' ? 'selected' : '' }}>Movies Only</option>
                    <option value="tv" {{ request('type') === 'tv' ? 'selected' : '' }}>TV Series Only</option>
                </select>

                <select name="status" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-orange-400">
                    <option value="">All Statuses</option>
                    <option value="offline_watched" {{ request('status') === 'offline_watched' ? 'selected' : '' }}>Watched Offline Only</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Stored in Vault Only</option>
                    <option value="downloading" {{ request('status') === 'downloading' ? 'selected' : '' }}>Downloading Only</option>
                    <option value="all_vault" {{ request('status') === 'all_vault' ? 'selected' : '' }}>All Active Vault Items</option>
                </select>

                <select name="period" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-orange-400">
                    <option value="" {{ !request('period') ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Today (Last 24h)</option>
                    <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Last 30 Days</option>
                </select>

                <select name="sort" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-orange-400">
                    <option value="recent" {{ request('sort') === 'recent' || !request('sort') ? 'selected' : '' }}>Sort: Most Recent</option>
                    <option value="size" {{ request('sort') === 'size' ? 'selected' : '' }}>Sort: Largest Size</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Sort: Oldest</option>
                </select>

                <button type="submit" class="px-5 py-2 rounded-xl bg-orange-500 hover:bg-orange-400 text-black text-xs font-bold transition-all shadow-md shadow-orange-500/20">
                    Apply Filters
                </button>

                @if(request('search') || request('type') || request('status') || request('period') || request('sort'))
                    <a href="{{ route('admin.downloads.index') }}" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 text-xs font-semibold transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- DETAILED DOWNLOADS TELEMETRY TABLE -->
    <div class="glass-panel rounded-3xl overflow-hidden border border-white/10 shadow-2xl">
        <div class="p-5 border-b border-white/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Offline Vault & Downloads Telemetry</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Real-time status of files saved to device vaults and offline viewing activity.</p>
            </div>
            <div class="text-xs font-mono text-slate-400">
                <span>{{ $downloads->total() }} events recorded</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-white/[0.02] text-slate-400 font-mono text-[10px] uppercase border-b border-white/5">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4 min-w-[240px]">Media Title</th>
                        <th class="py-3 px-4 text-center">Quality & Size</th>
                        <th class="py-3 px-4 text-center">Telemetry Status</th>
                        <th class="py-3 px-4 min-w-[180px]">User & Device</th>
                        <th class="py-3 px-4 text-right">Synced At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-300">
                    @forelse($downloads as $idx => $item)
                        @php
                            $rank = ($downloads->currentPage() - 1) * $downloads->perPage() + $idx + 1;
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
                                        <div class="font-extrabold text-sm text-white hover:text-orange-400 transition-colors">
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
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white/5 border border-white/10 font-mono text-xs">
                                    <span class="text-orange-400 font-bold">{{ $item->quality ?: '1080p' }}</span>
                                    <span class="text-slate-600">•</span>
                                    <span class="text-white">{{ $item->formatted_size }}</span>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                @if($item->status === 'offline_watched')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Watched Offline
                                    </span>
                                @elseif($item->status === 'completed')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-500/15 text-sky-400 border border-sky-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                                        Stored in Vault
                                    </span>
                                @elseif($item->status === 'downloading')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                                        Downloading
                                    </span>
                                @elseif($item->status === 'deleted')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        Deleted from Vault
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono bg-white/5 text-slate-400">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                @endif
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
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-right font-mono text-[11px] text-slate-400">
                                <div class="text-white font-semibold">
                                    {{ $item->created_at ? $item->created_at->diffForHumans() : 'N/A' }}
                                </div>
                                <div class="text-[10px] text-slate-500">
                                    {{ $item->created_at ? $item->created_at->format('d M Y, H:i') : '' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-14 text-center text-slate-400">
                                No download events found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($downloads->hasPages())
            <div class="p-4 border-t border-white/5">
                {{ $downloads->links() }}
            </div>
        @endif
    </div>

    <!-- LIVE OFFLINE WATCHED FEED -->
    @if($recentOfflineActivity->count() > 0)
        <div class="glass-panel rounded-3xl p-6 border border-white/10 space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Recent Offline Playback Activity</span>
                </h3>
                <span class="text-xs text-slate-500 font-mono">Real-time vault watches</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($recentOfflineActivity as $act)
                    <div class="p-3.5 rounded-2xl bg-[#12131A] border border-white/5 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 overflow-hidden">
                            @if($act->poster)
                                <img src="{{ $act->poster }}" class="w-8 h-11 rounded-lg object-cover bg-slate-800 flex-shrink-0" alt="{{ $act->title }}" />
                            @else
                                <div class="w-8 h-11 rounded-lg bg-slate-800 flex-shrink-0 flex items-center justify-center text-[10px] text-slate-500">🎬</div>
                            @endif
                            <div class="overflow-hidden">
                                <div class="text-xs font-bold text-white truncate">
                                    {{ $act->title }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5 truncate">
                                    Watched offline by <span class="text-orange-300 font-semibold">{{ $act->device->username ?? 'MovieCat User' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                Offline
                            </span>
                            <div class="text-[10px] text-slate-500 font-mono mt-1">
                                {{ $act->created_at ? $act->created_at->diffForHumans(null, true) : '' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
