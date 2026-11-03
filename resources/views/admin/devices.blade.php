@extends('layouts.app', ['title' => 'Active Devices Fleet'])

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white flex items-center gap-2.5">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Connected Devices Fleet & Automatic Usernames
            </h1>
            <p class="text-xs text-slate-400 mt-1">Users are assigned automatic usernames upon app launch. Inspect active sessions, playback history, and AdMob events.</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-xs font-bold border border-emerald-500/20">
                {{ $stats['activeNow'] }} Online Now
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-white/5 text-slate-300 text-xs font-semibold border border-white/10">
                {{ $stats['total'] }} Registered Fleet
            </span>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="glass-panel p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.devices.index') }}" class="flex-1 flex flex-wrap items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Username, Device ID, model, or IP..." class="flex-1 min-w-[220px] bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-[#FF2E4C]">
            
            <select name="platform" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200">
                <option value="">All Platforms</option>
                <option value="android" {{ request('platform') === 'android' ? 'selected' : '' }}>Android ({{ $stats['android'] }})</option>
                <option value="ios" {{ request('platform') === 'ios' ? 'selected' : '' }}>iOS ({{ $stats['ios'] }})</option>
            </select>

            <select name="status" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200">
                <option value="">All Devices</option>
                <option value="online" {{ request('status') === 'online' ? 'selected' : '' }}>Online Now Only</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-[#FF2E4C] hover:bg-[#E02440] text-white text-xs font-bold transition-all shadow-md shadow-[#FF2E4C]/25">
                Apply Filter
            </button>
            @if(request()->hasAny(['search', 'platform', 'status']))
                <a href="{{ route('admin.devices.index') }}" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 text-xs font-semibold">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Devices Table -->
    <div class="glass-panel p-6 rounded-3xl space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-white/10 text-slate-400 uppercase tracking-wider text-[11px]">
                        <th class="pb-3.5 font-semibold">Automatic Username / Device</th>
                        <th class="pb-3.5 font-semibold">Platform & OS</th>
                        <th class="pb-3.5 font-semibold">IP Address</th>
                        <th class="pb-3.5 font-semibold">App Sessions</th>
                        <th class="pb-3.5 font-semibold">Watch Telemetry</th>
                        <th class="pb-3.5 font-semibold">Last Seen</th>
                        <th class="pb-3.5 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($devices as $device)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-700 flex items-center justify-center text-white font-extrabold text-xs shadow border border-white/5 flex-shrink-0">
                                        {{ strtoupper(substr($device->display_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-white text-sm">{{ $device->display_name }}</span>
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">Auto Guest</span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                            {{ $device->device_name ?: 'Mobile' }} • <span class="text-slate-500">{{ $device->device_id }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $device->platform === 'ios' ? 'bg-indigo-500/20 text-indigo-300' : 'bg-emerald-500/20 text-emerald-300' }}">
                                    {{ $device->platform }}
                                </span>
                                <span class="text-slate-400 text-[11px] ml-1">{{ $device->os_version ?: '1.0' }}</span>
                            </td>
                            <td class="py-4 text-slate-400 font-mono text-[11px]">
                                {{ $device->ip_address ?: '127.0.0.1' }}
                            </td>
                            <td class="py-4 font-semibold text-slate-200">
                                {{ $device->sessions_count }} launches
                            </td>
                            <td class="py-4">
                                <span class="px-2.5 py-1 rounded-lg bg-white/5 text-slate-200 text-xs font-semibold border border-white/5">
                                    {{ $device->watch_progress_count ?? 0 }} items watched
                                </span>
                            </td>
                            <td class="py-4">
                                @if($device->isOnline())
                                    <span class="text-emerald-400 font-bold text-[11px] flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Online Now
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px]">{{ $device->last_active_at ? $device->last_active_at->diffForHumans() : 'Never' }}</span>
                                @endif
                            </td>
                            <td class="py-4 text-right">
                                <a href="{{ route('admin.devices.show', $device->device_id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-[#FF2E4C]/15 hover:bg-[#FF2E4C] text-[#FF2E4C] hover:text-white font-bold text-xs border border-[#FF2E4C]/30 transition-all shadow-sm">
                                    <span>Inspect Details</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                No mobile devices match this query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($devices->hasPages())
            <div class="pt-4 border-t border-white/5">
                {{ $devices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
