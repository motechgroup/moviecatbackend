@extends('layouts.app', ['title' => 'Downloads & Offline Tracking'])

@section('content')
<div class="space-y-6">

    <!-- HERO HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-3xl glass-panel relative overflow-hidden">
        <div class="space-y-1 relative z-10">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-500/10 text-orange-400 border border-orange-500/20">
                    Offline Vault Telemetry
                </span>
                <span class="text-xs text-slate-400">• Real-Time Tracking</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <span>Downloads & Offline Activity</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 max-w-2xl">
                Monitor user offline downloads, storage consumption, offline playback sessions, and most saved movies and series across your fleet.
            </p>
        </div>

        <div class="flex items-center gap-3 relative z-10">
            <span class="px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-xs font-mono text-slate-300">
                💾 Total Offline Data: <strong class="text-white">{{ $stats['totalData'] }}</strong>
            </span>
        </div>
    </div>

    
    <!-- MASTER DOWNLOAD SWITCH BANNER -->
    <div class="p-6 rounded-3xl glass-panel relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-4 border {{ $downloadsEnabled ? 'border-emerald-500/30 bg-emerald-950/10' : 'border-rose-500/30 bg-rose-950/10' }}">
        <div class="space-y-1">
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full {{ $downloadsEnabled ? 'bg-emerald-400 animate-pulse' : 'bg-rose-500' }}"></span>
                <h2 class="text-lg font-bold text-white">Downloads Master Switch: {{ $downloadsEnabled ? 'ENABLED' : 'DISABLED' }}</h2>
            </div>
            <p class="text-xs text-slate-400 max-w-xl">
                When turned <strong class="text-white">{{ $downloadsEnabled ? 'OFF' : 'ON' }}</strong>, mobile clients immediately receive <code>features.downloads = {{ $downloadsEnabled ? 'false' : 'true' }}</code> via remote config. Download buttons and vault actions across movies and series are {{ $downloadsEnabled ? 'silenced' : 'restored' }} in real-time.
            </p>
        </div>
        <form action="{{ route('admin.downloads.toggle') }}" method="POST">
            @csrf
            @if($downloadsEnabled)
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-rose-500/15 text-rose-400 hover:bg-rose-500/25 border border-rose-500/30 font-bold text-xs transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    Disable Downloads
                </button>
            @else
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-emerald-500/15 text-emerald-400 hover:bg-emerald-500/25 border border-emerald-500/30 font-bold text-xs transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Enable Downloads
                </button>
            @endif
        </form>
    </div>

    <!-- METRIC CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Downloads -->
        <div class="p-5 rounded-2xl glass-panel relative overflow-hidden group">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Downloads</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-white mt-1">{{ number_format($stats['total']) }}</div>
            <div class="text-[11px] text-emerald-400 mt-1 flex items-center gap-1">
                <span>{{ number_format($stats['completed']) }} completed successfully</span>
            </div>
        </div>

        <!-- Offline Storage -->
        <div class="p-5 rounded-2xl glass-panel relative overflow-hidden group">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Offline Data</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-orange-400 mt-1">{{ $stats['totalData'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Saved on user devices</div>
        </div>

        <!-- Offline Playbacks -->
        <div class="p-5 rounded-2xl glass-panel relative overflow-hidden group">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Offline Playbacks</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-sky-400 mt-1">{{ number_format($stats['offlineWatched']) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Watched with no internet</div>
        </div>

        <!-- Devices with Offline Media -->
        <div class="p-5 rounded-2xl glass-panel relative overflow-hidden group">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Offline Vaults</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-purple-400 mt-1">{{ number_format($stats['uniqueDevices']) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Devices holding downloads</div>
        </div>
    </div>

    <!-- MOST DOWNLOADED LEADERBOARDS (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Downloaded Movies -->
        <div class="p-6 rounded-3xl glass-panel space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="text-lg">🎬</span>
                    <span>Most Downloaded Movies</span>
                </h3>
                <span class="text-[10px] font-mono text-slate-400 uppercase tracking-wider">Top Rankings</span>
            </div>

            <div class="space-y-3">
                @forelse($topMovies as $idx => $m)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-white/[0.02] border border-white/5 hover:bg-white/[0.05] transition-all">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-xs font-bold text-white">#{{ $idx + 1 }}</span>
                            @if($m->poster)
                                <img src="{{ $m->poster }}" class="w-9 h-12 rounded-lg object-cover bg-slate-800" alt="{{ $m->title }}" />
                            @else
                                <div class="w-9 h-12 rounded-lg bg-slate-800 flex items-center justify-center text-[10px] text-slate-400">N/A</div>
                            @endif
                            <div class="overflow-hidden">
                                <div class="text-xs font-bold text-white truncate max-w-[180px] sm:max-w-xs">{{ $m->title }}</div>
                                <div class="text-[10px] text-slate-400">{{ $m->formatted_size }} downloaded</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-orange-500/15 text-orange-400 border border-orange-500/20">
                            {{ $m->count }} downloads
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">No movie downloads recorded yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Top Downloaded TV Shows -->
        <div class="p-6 rounded-3xl glass-panel space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="text-lg">📺</span>
                    <span>Most Downloaded TV Shows</span>
                </h3>
                <span class="text-[10px] font-mono text-slate-400 uppercase tracking-wider">Top Rankings</span>
            </div>

            <div class="space-y-3">
                @forelse($topSeries as $idx => $s)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-white/[0.02] border border-white/5 hover:bg-white/[0.05] transition-all">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-xs font-bold text-white">#{{ $idx + 1 }}</span>
                            @if($s->poster)
                                <img src="{{ $s->poster }}" class="w-9 h-12 rounded-lg object-cover bg-slate-800" alt="{{ $s->title }}" />
                            @else
                                <div class="w-9 h-12 rounded-lg bg-slate-800 flex items-center justify-center text-[10px] text-slate-400">N/A</div>
                            @endif
                            <div class="overflow-hidden">
                                <div class="text-xs font-bold text-white truncate max-w-[180px] sm:max-w-xs">{{ $s->title }}</div>
                                <div class="text-[10px] text-slate-400">{{ $s->formatted_size }} downloaded</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-500/15 text-purple-400 border border-purple-500/20">
                            {{ $s->count }} downloads
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">No series downloads recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="p-4 rounded-2xl glass-panel flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.downloads.index') }}" class="w-full flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search title, device ID, or automatic username..."
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-xs text-white placeholder-slate-400 focus:outline-none focus:border-orange-500"
                />
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select name="type" class="bg-slate-900 border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none">
                    <option value="">All Media Types</option>
                    <option value="movie" {{ request('type') === 'movie' ? 'selected' : '' }}>Movies Only</option>
                    <option value="tv" {{ request('type') === 'tv' ? 'selected' : '' }}>TV Series Only</option>
                </select>

                <select name="status" class="bg-slate-900 border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="offline_watched" {{ request('status') === 'offline_watched' ? 'selected' : '' }}>Offline Watched</option>
                    <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                </select>

                <button type="submit" class="px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold transition-all">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- LIVE DOWNLOADS STREAM TABLE -->
    <div class="glass-panel rounded-3xl overflow-hidden">
        <div class="p-5 border-b border-white/5 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white">Live Downloads & Offline Activity Stream</h3>
            <span class="text-xs text-slate-400">{{ $downloads->total() }} events logged</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-white/[0.02] text-slate-400 font-mono text-[10px] uppercase border-b border-white/5">
                    <tr>
                        <th class="py-3 px-4">Media</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Device & User</th>
                        <th class="py-3 px-4">Size & Quality</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-300">
                    @forelse($downloads as $item)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    @if($item->poster)
                                        <img src="{{ $item->poster }}" class="w-8 h-10 rounded object-cover bg-slate-800" alt="{{ $item->title }}" />
                                    @endif
                                    <div>
                                        <div class="font-bold text-white">{{ $item->title }}</div>
                                        @if($item->media_type === 'tv' && $item->season && $item->episode)
                                            <div class="text-[10px] text-purple-400">Season {{ $item->season }}, Episode {{ $item->episode }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $item->media_type === 'movie' ? 'bg-sky-500/15 text-sky-400 border border-sky-500/20' : 'bg-purple-500/15 text-purple-400 border border-purple-500/20' }}">
                                    {{ $item->media_type }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-white">
                                    {{ $item->device->username ?? ('MovieCat #' . strtoupper(substr(md5($item->device_id), 0, 4))) }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $item->device_id }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-orange-400">{{ $item->formatted_size }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $item->quality ?: '1080p' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($item->status === 'completed')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">
                                        Completed
                                    </span>
                                @elseif($item->status === 'offline_watched')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-500/15 text-sky-400 border border-sky-500/20">
                                        Offline Watched
                                    </span>
                                @elseif($item->status === 'deleted')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/15 text-rose-400 border border-rose-500/20">
                                        Deleted
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white/10 text-slate-300">
                                        {{ $item->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-400 text-[11px]">
                                {{ $item->created_at ? $item->created_at->diffForHumans() : 'Just now' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                No download telemetry events recorded yet.
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

</div>
@endsection
