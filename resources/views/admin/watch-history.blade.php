@extends('layouts.app', ['title' => 'Watch Progress Stream'])

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white flex items-center gap-2.5">
                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Watch Progress Telemetry Stream
            </h1>
            <p class="text-xs text-slate-400 mt-1">Real-time sync of user viewing checkpoints and playback completion across all mobile devices.</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-purple-500/10 text-purple-400 text-xs font-bold border border-purple-500/20">
                {{ $stats['totalCheckpoints'] }} Total Checkpoints
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-xs font-bold border border-emerald-500/20">
                {{ $stats['completed'] }} Finished Streams
            </span>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="glass-panel p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.sync.index') }}" class="flex-1 flex flex-wrap items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Movie / Series title, username, or device ID..." class="flex-1 min-w-[240px] bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-[#FF2E4C]">
            
            <select name="type" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200">
                <option value="">All Types</option>
                <option value="movie" {{ request('type') === 'movie' ? 'selected' : '' }}>Movies</option>
                <option value="tv" {{ request('type') === 'tv' ? 'selected' : '' }}>TV Series</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-[#FF2E4C] hover:bg-[#E02440] text-white text-xs font-bold transition-all shadow-md shadow-[#FF2E4C]/25">
                Search
            </button>
            @if(request()->hasAny(['search', 'type']))
                <a href="{{ route('admin.sync.index') }}" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 text-xs font-semibold">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="glass-panel p-6 rounded-3xl space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-white/10 text-slate-400 uppercase tracking-wider text-[11px]">
                        <th class="pb-3.5 font-semibold">Title</th>
                        <th class="pb-3.5 font-semibold">Type</th>
                        <th class="pb-3.5 font-semibold">Automatic Username / Device</th>
                        <th class="pb-3.5 font-semibold">Playback Position</th>
                        <th class="pb-3.5 font-semibold">Status</th>
                        <th class="pb-3.5 font-semibold text-right">Synced At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($history as $item)
                        @php
                            $pct = $item->duration > 0 ? min(100, round(($item->current_time / $item->duration) * 100)) : 0;
                            $displayName = $item->device->display_name ?? 'Guest User';
                        @endphp
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-3.5 font-bold text-white flex items-center gap-3">
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
                            <td class="py-3.5">
                                @if($item->media_type === 'tv')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/20 text-purple-300">
                                        TV
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-300">
                                        MOVIE
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5">
                                <a href="{{ route('admin.devices.show', $item->device_id) }}" class="inline-flex items-center gap-2 group">
                                    <div class="w-6 h-6 rounded-lg bg-gradient-to-tr from-slate-800 to-slate-700 flex items-center justify-center text-white font-bold text-[10px] group-hover:scale-105 transition-transform">
                                        {{ strtoupper(substr($displayName, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-white group-hover:text-[#FF2E4C] transition-colors">{{ $displayName }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono">{{ substr($item->device_id, 0, 14) }}...</div>
                                    </div>
                                </a>
                            </td>
                            <td class="py-3.5">
                                <div class="w-36">
                                    <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1 font-mono">
                                        <span>{{ gmdate('H:i:s', (int)$item->current_time) }}</span>
                                        <span>{{ $pct }}%</span>
                                    </div>
                                    <div class="w-full h-1.5 rounded-full bg-white/10 overflow-hidden">
                                        <div class="h-full bg-[#FF2E4C] rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5">
                                @if($item->completed || $pct >= 90)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Completed</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">In Progress</span>
                                @endif
                            </td>
                            <td class="py-3.5 text-right font-mono text-[11px] text-slate-400">
                                {{ $item->last_watched_at ? $item->last_watched_at->diffForHumans() : 'Just now' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">No playback sync sessions logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($history->hasPages())
            <div class="pt-4 border-t border-white/5">
                {{ $history->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
