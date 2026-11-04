@extends('layouts.app', ['title' => 'App Error Telemetry & Debug Logs'])

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ 
    activeModal: null,
    copiedToast: false,
    toastMessage: '',
    copyToClipboard(text, msg = 'Copied to clipboard!') {
        navigator.clipboard.writeText(text).then(() => {
            this.toastMessage = msg;
            this.copiedToast = true;
            setTimeout(() => this.copiedToast = false, 3000);
        });
    }
}">
    <!-- Toast Notification -->
    <div x-show="copiedToast" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed bottom-6 right-6 z-50 flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-emerald-500/90 text-white font-bold text-xs shadow-2xl backdrop-blur-md border border-emerald-400/30"
         style="display: none;">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span x-text="toastMessage"></span>
    </div>

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-2.5">
                <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Mobile App Error Telemetry & Debug Center
            </h1>
            <p class="text-xs text-slate-400 mt-1">
                Real-time error logs and stack traces captured from client devices. Export structured reports directly to debug issues with developers or AI assistants.
            </p>
        </div>

        <div class="flex items-center flex-wrap gap-2.5">
            <!-- Export Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="px-4 py-2 rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-rose-500/25 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Errors &rarr;
                </button>
                <div x-show="open" 
                     @click.away="open = false" 
                     class="absolute right-0 mt-2 w-64 glass-panel rounded-2xl border border-white/10 shadow-2xl p-2 z-50 space-y-1"
                     style="display: none;">
                    <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Export Filtered Data</div>
                    <a href="{{ route('admin.errors.export', array_merge(request()->query(), ['format' => 'markdown'])) }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-200 hover:text-white hover:bg-white/10 transition-colors">
                        <span class="text-base">📄</span>
                        <div>
                            <div class="font-bold">Debug Report (.md)</div>
                            <div class="text-[10px] text-slate-400">Formatted for AI & Developers</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.errors.export', array_merge(request()->query(), ['format' => 'json'])) }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-200 hover:text-white hover:bg-white/10 transition-colors">
                        <span class="text-base">⚙️</span>
                        <div>
                            <div class="font-bold">Raw JSON (.json)</div>
                            <div class="text-[10px] text-slate-400">Structured telemetry array</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.errors.export', array_merge(request()->query(), ['format' => 'csv'])) }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-200 hover:text-white hover:bg-white/10 transition-colors">
                        <span class="text-base">📊</span>
                        <div>
                            <div class="font-bold">Spreadsheet CSV (.csv)</div>
                            <div class="text-[10px] text-slate-400">Excel / Google Sheets tabular data</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Mark All Resolved Form -->
            <form action="{{ route('admin.errors.resolveAll') }}" method="POST" onsubmit="return confirm('Mark all open error logs as resolved?')">
                @csrf
                <button type="submit" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-emerald-400 hover:text-emerald-300 text-xs font-semibold border border-white/10 transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Mark All Resolved
                </button>
            </form>

            <!-- Clear Resolved Form -->
            <form action="{{ route('admin.errors.clear') }}" method="POST" onsubmit="return confirm('Permanently delete all resolved error logs?')">
                @csrf
                <button type="submit" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 hover:text-rose-400 text-xs font-semibold border border-white/10 transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                @endif
            </div>
            <div class="text-3xl font-black text-rose-400 mt-2">{{ $stats['unresolved'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1 group-hover:text-rose-300 transition-colors">
                <span>Filter unresolved</span>
                <span>&rarr;</span>
            </div>
        </a>

        <a href="{{ route('admin.errors.index', ['severity' => 'fatal']) }}" class="glass-panel p-5 rounded-3xl hover:border-amber-500/30 transition-all block group">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Fatal Crashes</span>
                <span class="text-amber-400">🔥</span>
            </div>
            <div class="text-3xl font-black text-amber-400 mt-2">{{ $stats['fatal'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1 group-hover:text-amber-300 transition-colors">
                <span>Critical app crashes</span>
                <span>&rarr;</span>
            </div>
        </a>

        <div class="glass-panel p-5 rounded-3xl">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Last 24 Hours</div>
            <div class="text-3xl font-black text-white mt-2">{{ $stats['last24h'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Recent telemetry velocity</div>
        </div>

        <div class="glass-panel p-5 rounded-3xl">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Recorded</div>
            <div class="text-3xl font-black text-slate-300 mt-2">{{ $stats['total'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Lifetime captured issues</div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="glass-panel p-4 rounded-2xl">
        <form method="GET" action="{{ route('admin.errors.index') }}" class="flex flex-wrap items-center gap-2">
            <div class="flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search error message, screen, device ID or handle..." class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-rose-500">
            </div>

            <select name="severity" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none">
                <option value="">All Severities</option>
                <option value="fatal" {{ request('severity') === 'fatal' ? 'selected' : '' }}>Fatal Crash</option>
                <option value="error" {{ request('severity') === 'error' ? 'selected' : '' }}>Error</option>
                <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>Warning</option>
            </select>

            <select name="screen" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none">
                <option value="">All Screens</option>
                @foreach($screens as $s)
                    <option value="{{ $s->screen }}" {{ request('screen') === $s->screen ? 'selected' : '' }}>
                        {{ ucfirst($s->screen) }} ({{ $s->count }})
                    </option>
                @endforeach
            </select>

            <select name="status" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none">
                <option value="">All Statuses</option>
                <option value="unresolved" {{ request('status') === 'unresolved' ? 'selected' : '' }}>Unresolved Only</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved Only</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold transition-all shadow-md shadow-rose-500/25">
                Apply Filter
            </button>

            @if(request()->hasAny(['search', 'severity', 'screen', 'status']))
                <a href="{{ route('admin.errors.index') }}" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 text-xs font-semibold transition-colors">
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
                        <th class="pb-3.5 font-semibold">Severity & Screen</th>
                        <th class="pb-3.5 font-semibold">Error Message & Diagnostics</th>
                        <th class="pb-3.5 font-semibold">Device & Environment</th>
                        <th class="pb-3.5 font-semibold">Status</th>
                        <th class="pb-3.5 font-semibold">Reported At</th>
                        <th class="pb-3.5 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($errors as $error)
                        @php
                            $dev = $error->device;
                            $devId = $error->device_id ?: 'Unknown';
                            $devUser = $dev?->display_name ?: ('Device #' . substr(md5($devId), 0, 4));
                            $platform = strtoupper($error->platform ?: ($dev?->platform ?? 'ANDROID'));
                            $os = $dev?->os_version ?: 'OS';
                            $appVer = $error->app_version ?: ($dev?->app_version ?? '1.0.0');
                            $countryFlag = $dev?->country_flag ?: '🌐';
                            $country = $dev?->country ?: ($dev?->country_code ?: 'Unknown');
                            $ip = $dev?->ip_address ?: '127.0.0.1';
                            $timeStr = $error->created_at ? $error->created_at->format('M d, Y H:i:s') : 'Unknown';

                            // Formatted AI prompt for 1-click clipboard copy
                            $promptText = "I need help fixing this MovieCat app issue:\n" .
                                "- Severity: " . strtoupper($error->severity) . "\n" .
                                "- Screen: " . ($error->screen ?: 'global') . "\n" .
                                "- Error Message: " . $error->error_message . "\n" .
                                "- Device: " . $devId . " (" . $devUser . ")\n" .
                                "- OS & Platform: " . $platform . " " . $os . "\n" .
                                "- App Version: " . $appVer . "\n" .
                                (!empty($error->stack_trace) ? "- Stack Trace:\n" . $error->stack_trace : "");
                            $promptJson = json_encode($promptText);
                        @endphp
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <!-- Severity & Screen -->
                            <td class="py-4 align-top">
                                <div class="flex flex-col gap-1.5 items-start">
                                    @if($error->severity === 'fatal')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-pulse"></span>
                                            FATAL CRASH
                                        </span>
                                    @elseif($error->severity === 'error')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                            ERROR
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">
                                            WARNING
                                        </span>
                                    @endif

                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-white/5 text-slate-300 border border-white/10">
                                        📺 {{ $error->screen ?: 'global' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Error Message & Diagnostics -->
                            <td class="py-4 align-top max-w-md">
                                <div class="font-bold text-white break-words text-sm">{{ $error->error_message }}</div>
                                
                                <div class="flex items-center gap-2 mt-2 flex-wrap">
                                    @if($error->stack_trace)
                                        <span class="px-2 py-0.5 rounded text-[9px] font-semibold bg-sky-500/15 text-sky-300 border border-sky-500/20">
                                            Stack Trace Available
                                        </span>
                                    @endif
                                    @if($error->metadata)
                                        <span class="px-2 py-0.5 rounded text-[9px] font-semibold bg-purple-500/15 text-purple-300 border border-purple-500/20">
                                            Metadata Attached
                                        </span>
                                    @endif
                                    <span class="text-[10px] text-slate-500 font-mono">
                                        Type: {{ $error->error_type }}
                                    </span>
                                </div>
                            </td>

                            <!-- Device & User -->
                            <td class="py-4 align-top">
                                @if($dev)
                                    <a href="{{ route('admin.devices.show', $dev->device_id) }}" class="inline-flex items-center gap-2 group">
                                        <div class="w-7 h-7 rounded-xl bg-gradient-to-tr from-slate-800 to-slate-700 flex items-center justify-center text-white font-bold text-xs border border-white/5">
                                            {{ strtoupper(substr($dev->display_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-white group-hover:text-rose-400 transition-colors">{{ $dev->display_name }}</div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                <span>{{ $countryFlag }} {{ $country }}</span> • <span class="uppercase text-slate-300">{{ $platform }}</span>
                                            </div>
                                            <div class="text-[9px] text-slate-500 font-mono mt-0.5">v{{ $appVer }} • {{ $dev->device_id }}</div>
                                        </div>
                                    </a>
                                @else
                                    <div>
                                        <span class="font-mono text-slate-300 text-xs">{{ $devId }}</span>
                                        <div class="text-[10px] text-slate-500 uppercase mt-0.5">{{ $platform }} • v{{ $appVer }}</div>
                                    </div>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-4 align-top">
                                @if($error->resolved)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 flex items-center gap-1 w-max">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Resolved
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30 flex items-center gap-1 w-max">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                        Open
                                    </span>
                                @endif
                            </td>

                            <!-- Reported At -->
                            <td class="py-4 align-top font-mono text-[11px] text-slate-400">
                                <div>{{ $error->created_at ? $error->created_at->diffForHumans() : 'Just now' }}</div>
                                <div class="text-[9px] text-slate-500">{{ $timeStr }}</div>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 align-top text-right space-x-1.5 whitespace-nowrap">
                                <!-- Inspect Modal Trigger -->
                                <button type="button" 
                                        @click="activeModal = {{ $error->id }}"
                                        class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white text-[11px] font-bold transition-colors inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Inspect
                                </button>

                                <!-- 1-Click Copy AI Prompt -->
                                <button type="button" 
                                        @click="copyToClipboard({{ $promptJson }}, 'Copied debug prompt for AI!')"
                                        title="Copy formatted prompt for AI assistant (Claude/GPT/Gemini)"
                                        class="p-1.5 rounded-lg bg-purple-500/15 hover:bg-purple-500/25 text-purple-300 border border-purple-500/30 transition-colors inline-flex items-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                </button>

                                <!-- Resolve Toggle -->
                                <form action="{{ route('admin.errors.resolve', $error->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="px-2.5 py-1 rounded-lg {{ $error->resolved ? 'bg-white/5 text-slate-400 hover:text-white' : 'bg-emerald-500/15 text-emerald-400 hover:bg-emerald-500/25 border border-emerald-500/30' }} text-[11px] font-semibold transition-colors">
                                        {{ $error->resolved ? 'Reopen' : 'Resolve' }}
                                    </button>
                                </form>

                                <!-- Delete -->
                                <form action="{{ route('admin.errors.delete', $error->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this error log?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-400 hover:bg-white/5 transition-colors" title="Delete Log">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>

                                <!-- Full Detail Modal -->
                                <div x-show="activeModal === {{ $error->id }}" 
                                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4 text-left"
                                     style="display: none;">
                                    <div class="glass-panel p-6 sm:p-8 rounded-3xl max-w-3xl w-full border border-white/10 shadow-2xl max-h-[90vh] overflow-y-auto space-y-6"
                                         @click.away="activeModal = null">
                                        
                                        <!-- Modal Header -->
                                        <div class="flex items-start justify-between gap-4 border-b border-white/10 pb-4">
                                            <div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    @if($error->severity === 'fatal')
                                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-500/20 text-rose-400 border border-rose-500/40">FATAL CRASH</span>
                                                    @elseif($error->severity === 'error')
                                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/40">ERROR</span>
                                                    @else
                                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-yellow-500/20 text-yellow-300">WARNING</span>
                                                    @endif
                                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono bg-white/10 text-slate-300">Screen: {{ $error->screen ?: 'global' }}</span>
                                                    <span class="text-xs text-slate-400 font-mono">Issue #{{ $error->id }}</span>
                                                </div>
                                                <h3 class="text-xl font-bold text-white mt-2 break-words">{{ $error->error_message }}</h3>
                                            </div>
                                            <button type="button" @click="activeModal = null" class="text-slate-400 hover:text-white text-2xl font-bold">&times;</button>
                                        </div>

                                        <!-- Device & Environment Card -->
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-2xl bg-white/5 border border-white/5 text-xs">
                                            <div>
                                                <div class="text-[10px] text-slate-400 uppercase font-semibold">User & Device ID</div>
                                                <div class="font-bold text-white mt-0.5 truncate">{{ $devUser }}</div>
                                                <div class="font-mono text-[10px] text-slate-500 truncate">{{ $devId }}</div>
                                            </div>
                                            <div>
                                                <div class="text-[10px] text-slate-400 uppercase font-semibold">Platform & OS</div>
                                                <div class="font-bold text-slate-200 mt-0.5 uppercase">{{ $platform }}</div>
                                                <div class="text-[10px] text-slate-400 truncate">{{ $os }}</div>
                                            </div>
                                            <div>
                                                <div class="text-[10px] text-slate-400 uppercase font-semibold">App Version & IP</div>
                                                <div class="font-bold text-slate-200 mt-0.5">v{{ $appVer }}</div>
                                                <div class="font-mono text-[10px] text-slate-400 truncate">{{ $ip }}</div>
                                            </div>
                                            <div>
                                                <div class="text-[10px] text-slate-400 uppercase font-semibold">Reported Location</div>
                                                <div class="font-bold text-white mt-0.5 flex items-center gap-1">
                                                    <span>{{ $countryFlag }}</span>
                                                    <span>{{ $country }}</span>
                                                </div>
                                                <div class="text-[10px] text-slate-400">{{ $timeStr }}</div>
                                            </div>
                                        </div>

                                        <!-- Stack Trace Box -->
                                        @if($error->stack_trace)
                                            <div>
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                                                        <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                                        Stack Trace
                                                    </div>
                                                    <button type="button" 
                                                            @click="copyToClipboard({{ json_encode($error->stack_trace) }}, 'Stack trace copied!')"
                                                            class="text-[11px] font-semibold text-sky-400 hover:text-sky-300 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                                        Copy Trace
                                                    </button>
                                                </div>
                                                <pre class="p-4 rounded-2xl bg-[#0B0C10] border border-white/10 text-emerald-400 font-mono text-[11px] overflow-x-auto max-h-60 whitespace-pre-wrap leading-relaxed">{{ $error->stack_trace }}</pre>
                                            </div>
                                        @endif

                                        <!-- Metadata Context -->
                                        @if($error->metadata)
                                            <div>
                                                <div class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Context Metadata</div>
                                                <pre class="p-4 rounded-2xl bg-[#0B0C10] border border-white/10 text-amber-300 font-mono text-[11px] overflow-x-auto max-h-40 whitespace-pre-wrap">{{ is_array($error->metadata) ? json_encode($error->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $error->metadata }}</pre>
                                            </div>
                                        @endif

                                        <!-- Modal Actions Footer -->
                                        <div class="flex items-center justify-between gap-3 pt-4 border-t border-white/10 flex-wrap">
                                            <button type="button" 
                                                    @click="copyToClipboard({{ $promptJson }}, 'Copied debug prompt for AI!')"
                                                    class="px-4 py-2 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 text-xs font-bold flex items-center gap-2 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                                Copy Debug Prompt for AI
                                            </button>

                                            <div class="flex items-center gap-2">
                                                <form action="{{ route('admin.errors.resolve', $error->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 rounded-xl {{ $error->resolved ? 'bg-white/10 text-slate-300 hover:text-white' : 'bg-emerald-500 hover:bg-emerald-600 text-white' }} text-xs font-bold transition-all shadow-md">
                                                        {{ $error->resolved ? 'Reopen Issue' : 'Mark as Resolved' }}
                                                    </button>
                                                </form>
                                                <button type="button" @click="activeModal = null" class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-semibold">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-slate-400">
                                <svg class="w-10 h-10 text-emerald-400 mx-auto mb-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div class="text-white font-bold text-base">No Error Telemetry Found</div>
                                <div class="text-xs text-slate-400 mt-1">Client applications are running cleanly without any reported runtime faults.</div>
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
