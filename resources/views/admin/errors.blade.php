@extends('layouts.app', ['title' => 'App Error Telemetry Logs'])

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white flex items-center gap-2.5">
                <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Mobile App Error Telemetry & Crash Logs
            </h1>
            <p class="text-xs text-slate-400 mt-1">Real-time error reports automatically captured and transmitted from users' mobile devices.</p>
        </div>

        <div class="flex items-center gap-3">
            <form action="{{ route('admin.errors.clear') }}" method="POST" onsubmit="return confirm('Clear all resolved error logs?')">
                @csrf
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-semibold border border-white/10 transition-colors">
                    Clear Resolved
                </button>
            </form>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.errors.index', ['status' => 'unresolved']) }}" class="glass-panel p-5 rounded-3xl hover:border-rose-500/30 transition-all block group">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Unresolved Issues</span>
                @if($stats['unresolved'] > 0)
                    <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
                @endif
            </div>
            <div class="text-3xl font-extrabold text-rose-400 mt-2">{{ $stats['unresolved'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Requires admin attention &rarr;</div>
        </a>

        <a href="{{ route('admin.errors.index', ['severity' => 'fatal']) }}" class="glass-panel p-5 rounded-3xl hover:border-amber-500/30 transition-all block group">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Fatal Crashes</div>
            <div class="text-3xl font-extrabold text-amber-400 mt-2">{{ $stats['fatal'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">App termination events &rarr;</div>
        </a>

        <div class="glass-panel p-5 rounded-3xl">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Last 24 Hours</div>
            <div class="text-3xl font-extrabold text-white mt-2">{{ $stats['last24h'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Recent telemetry rate</div>
        </div>

        <div class="glass-panel p-5 rounded-3xl">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Recorded</div>
            <div class="text-3xl font-extrabold text-slate-300 mt-2">{{ $stats['total'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Lifetime captured issues</div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="glass-panel p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.errors.index') }}" class="flex-1 flex flex-wrap items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by error message, screen, device or username..." class="flex-1 min-w-[240px] bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-rose-500">
            
            <select name="severity" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200">
                <option value="">All Severities</option>
                <option value="fatal" {{ request('severity') === 'fatal' ? 'selected' : '' }}>Fatal Crash</option>
                <option value="error" {{ request('severity') === 'error' ? 'selected' : '' }}>Error</option>
                <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>Warning</option>
            </select>

            <select name="screen" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200">
                <option value="">All Screens</option>
                @foreach($screens as $s)
                    <option value="{{ $s->screen }}" {{ request('screen') === $s->screen ? 'selected' : '' }}>
                        {{ ucfirst($s->screen) }} ({{ $s->count }})
                    </option>
                @endforeach
            </select>

            <select name="status" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200">
                <option value="">All Statuses</option>
                <option value="unresolved" {{ request('status') === 'unresolved' ? 'selected' : '' }}>Unresolved Only</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold transition-all shadow-md shadow-rose-500/25">
                Filter
            </button>
            @if(request()->hasAny(['search', 'severity', 'screen', 'status']))
                <a href="{{ route('admin.errors.index') }}" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 text-xs font-semibold">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Error Logs Table -->
    <div class="glass-panel p-6 rounded-3xl space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-white/10 text-slate-400 uppercase tracking-wider text-[11px]">
                        <th class="pb-3.5 font-semibold">Severity / Screen</th>
                        <th class="pb-3.5 font-semibold">Error Message</th>
                        <th class="pb-3.5 font-semibold">Device / User</th>
                        <th class="pb-3.5 font-semibold">Status</th>
                        <th class="pb-3.5 font-semibold">Reported At</th>
                        <th class="pb-3.5 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($errors as $error)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-4 align-top">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if($error->severity === 'fatal')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-pulse"></span>
                                            FATAL
                                        </span>
                                    @elseif($error->severity === 'error')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                            ERROR
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-500/20 text-yellow-300">
                                            WARNING
                                        </span>
                                    @endif

                                    @if($error->screen)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-white/5 text-slate-300 border border-white/10">
                                            {{ $error->screen }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-4 align-top max-w-md">
                                <div class="font-bold text-white break-words text-sm">{{ $error->error_message }}</div>
                                @if($error->stack_trace)
                                    <details class="mt-2 text-[11px] text-slate-400 font-mono">
                                        <summary class="cursor-pointer text-sky-400 hover:underline">View Stack Trace & Details</summary>
                                        <pre class="mt-2 p-3 rounded-xl bg-black/40 border border-white/10 text-slate-300 overflow-x-auto text-[10px] max-h-48 whitespace-pre-wrap">{{ $error->stack_trace }}</pre>
                                    </details>
                                @endif
                                @if($error->metadata)
                                    <div class="mt-1 text-[10px] text-slate-500 font-mono">
                                        Meta: {{ json_encode($error->metadata) }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-4 align-top">
                                @if($error->device)
                                    <a href="{{ route('admin.devices.show', $error->device->device_id) }}" class="inline-flex items-center gap-2 group">
                                        <div class="w-7 h-7 rounded-xl bg-gradient-to-tr from-slate-800 to-slate-700 flex items-center justify-center text-white font-bold text-xs">
                                            {{ strtoupper(substr($error->device->display_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-white group-hover:text-rose-400 transition-colors">{{ $error->device->display_name }}</div>
                                            <div class="text-[10px] text-slate-500">{{ $error->device->country_display }} • {{ strtoupper($error->platform ?: $error->device->platform) }}</div>
                                        </div>
                                    </a>
                                @else
                                    <span class="text-slate-400 font-mono text-[11px]">{{ substr($error->device_id ?? 'Unknown', 0, 12) }}...</span>
                                @endif
                            </td>

                            <td class="py-4 align-top">
                                @if($error->resolved)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Resolved</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">Open</span>
                                @endif
                            </td>

                            <td class="py-4 align-top font-mono text-[11px] text-slate-400">
                                {{ $error->created_at ? $error->created_at->diffForHumans() : 'Just now' }}
                            </td>

                            <td class="py-4 align-top text-right space-x-1.5 whitespace-nowrap">
                                <form action="{{ route('admin.errors.resolve', $error->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-lg {{ $error->resolved ? 'bg-white/5 text-slate-400 hover:text-white' : 'bg-emerald-500/15 text-emerald-400 hover:bg-emerald-500/25 border border-emerald-500/30' }} text-[11px] font-semibold transition-colors">
                                        {{ $error->resolved ? 'Reopen' : 'Mark Resolved' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.errors.delete', $error->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this error log?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 rounded-lg text-slate-500 hover:text-rose-400 hover:bg-white/5 transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <svg class="w-8 h-8 text-emerald-400 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                No application errors logged. Client applications are running smoothly!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($errors->hasPages())
            <div class="pt-4 border-t border-white/5">
                {{ $errors->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
