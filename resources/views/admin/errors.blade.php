@extends('layouts.app', ['title' => 'App Error Telemetry & Debug Logs'])

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Global Toast Notification (z-[99999] so always visible above modals) -->
    <div id="globalToast" 
         class="hidden fixed bottom-6 right-6 z-[99999] flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-emerald-500 text-white font-bold text-xs shadow-2xl backdrop-blur-md border border-emerald-400/30 transition-all duration-300">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span id="globalToastMessage">Copied to clipboard!</span>
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
                Real-time error diagnostics grouped by mobile application modules (Player, Downloads, VIP, Search, Catalogue, History, Ads, Core).
            </p>
        </div>

        <div class="flex items-center flex-wrap gap-2.5">
            <!-- Export Dropdown -->
            <div class="relative">
                <button type="button" 
                        id="exportDropdownBtn"
                        onclick="toggleExportDropdown()" 
                        class="px-4 py-2 rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-rose-500/25 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Errors &rarr;
                </button>
                <div id="exportDropdownMenu" 
                     class="hidden absolute right-0 mt-2 w-64 glass-panel rounded-2xl border border-white/10 shadow-2xl p-2 z-50 space-y-1">
                    <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Export Filtered Data</div>
                    <a href="{{ route('admin.errors.export', array_merge(request()->query(), ['format' => 'markdown'])) }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-200 hover:text-white hover:bg-white/10 transition-colors">
                        <span class="text-base">📄</span>
                        <div>
                            <div class="font-bold">Debug Report (.md)</div>
                            <div class="text-[10px] text-slate-400">Formatted with Modules for AI</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.errors.export', array_merge(request()->query(), ['format' => 'json'])) }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-200 hover:text-white hover:bg-white/10 transition-colors">
                        <span class="text-base">⚙️</span>
                        <div>
                            <div class="font-bold">Raw JSON (.json)</div>
                            <div class="text-[10px] text-slate-400">Structured telemetry array with module tags</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.errors.export', array_merge(request()->query(), ['format' => 'csv'])) }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-200 hover:text-white hover:bg-white/10 transition-colors">
                        <span class="text-base">📊</span>
                        <div>
                            <div class="font-bold">Spreadsheet CSV (.csv)</div>
                            <div class="text-[10px] text-slate-400">Excel / Google Sheets with Module column</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Mark All Resolved Form -->
            <form action="{{ route('admin.errors.resolveAll') }}" method="POST" onsubmit="return confirm('Mark all open error logs across all modules as resolved?')">
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

    <!-- Overall Metrics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.errors.index', array_merge(request()->query(), ['status' => 'unresolved'])) }}" class="glass-panel p-5 rounded-3xl hover:border-rose-500/30 transition-all block group {{ request('status') === 'unresolved' ? 'border-rose-500/50 bg-rose-500/5' : '' }}">
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

        <a href="{{ route('admin.errors.index', array_merge(request()->query(), ['severity' => 'fatal'])) }}" class="glass-panel p-5 rounded-3xl hover:border-amber-500/30 transition-all block group {{ request('severity') === 'fatal' ? 'border-amber-500/50 bg-amber-500/5' : '' }}">
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

        <a href="{{ route('admin.errors.index') }}" class="glass-panel p-5 rounded-3xl hover:border-white/20 transition-all block group">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Recorded</div>
            <div class="text-3xl font-black text-slate-300 mt-2">{{ $stats['total'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1 group-hover:text-white transition-colors">
                <span>Reset all filters</span>
                <span>&rarr;</span>
            </div>
        </a>
    </div>

    <!-- APPLICATION MODULES HUB / BENTO GRID -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-base font-extrabold text-white tracking-tight">App Modules Health & Error Breakdown</span>
                <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded-full bg-white/5 text-slate-400 border border-white/10">8 Core Modules</span>
            </div>
            @if(request('module'))
                <a href="{{ route('admin.errors.index', request()->except('module')) }}" class="text-xs font-bold text-rose-400 hover:text-rose-300 flex items-center gap-1">
                    <span>Clear Module Filter (Showing: {{ $modulesDef[request('module')]['name'] ?? request('module') }})</span>
                    <span class="text-base">&times;</span>
                </a>
            @endif
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5">
            @foreach($modulesDef as $mKey => $mDef)
                @php
                    $mStat = $moduleStats[$mKey] ?? [
                        'total' => 0,
                        'unresolved' => 0,
                        'fatal' => 0,
                        'percentage' => 0,
                        'status_color' => 'emerald',
                        'status_label' => 'Healthy',
                    ];
                    $isActiveMod = request('module') === $mKey;
                @endphp
                <a href="{{ route('admin.errors.index', array_merge(request()->query(), ['module' => $isActiveMod ? null : $mKey])) }}" 
                   class="glass-panel p-4 rounded-2xl border transition-all block group relative overflow-hidden {{ $isActiveMod ? 'border-rose-500 bg-rose-500/10 shadow-lg shadow-rose-500/20' : 'hover:border-white/20 hover:bg-white/[0.03]' }}">
                    
                    <!-- Top row: Icon & Status Pill -->
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">{{ $mDef['icon'] }}</span>
                            <div>
                                <div class="font-extrabold text-white text-xs leading-tight group-hover:text-rose-300 transition-colors {{ $isActiveMod ? 'text-rose-300' : '' }}">
                                    {{ $mDef['short_name'] }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $mStat['total'] }} {{ Str::plural('issue', $mStat['total']) }} ({{ $mStat['percentage'] }}%)
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        @if($mStat['status_color'] === 'rose')
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center gap-1 shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-pulse"></span>
                                CRITICAL
                            </span>
                        @elseif($mStat['status_color'] === 'amber')
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30 shrink-0">
                                ATTENTION
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 shrink-0">
                                HEALTHY
                            </span>
                        @endif
                    </div>

                    <!-- Bottom row: Badges -->
                    <div class="flex items-center gap-2 mt-3 pt-2.5 border-t border-white/5 flex-wrap">
                        @if($mStat['unresolved'] > 0)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/20">
                                {{ $mStat['unresolved'] }} Open
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/20">
                                0 Open
                            </span>
                        @endif

                        @if($mStat['fatal'] > 0)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                🔥 {{ $mStat['fatal'] }} Fatal
                            </span>
                        @endif

                        <div class="ml-auto text-[10px] text-slate-500 font-mono">
                            {{ $isActiveMod ? 'Filtered ✓' : 'Filter →' }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="glass-panel p-4 rounded-2xl">
        <form method="GET" action="{{ route('admin.errors.index') }}" class="flex flex-wrap items-center gap-2">
            <!-- Retain current view mode -->
            <input type="hidden" name="view" value="{{ $viewMode }}">

            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search error message, screen, device ID or handle..." class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-rose-500">
            </div>

            <!-- Module Filter Dropdown -->
            <select name="module" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none">
                <option value="">All App Modules</option>
                @foreach($modulesDef as $k => $m)
                    <option value="{{ $k }}" {{ request('module') === $k ? 'selected' : '' }}>
                        {{ $m['icon'] }} {{ $m['name'] }} ({{ $moduleStats[$k]['total'] ?? 0 }})
                    </option>
                @endforeach
            </select>

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
                Apply Filters
            </button>

            @if(request()->hasAny(['search', 'severity', 'screen', 'status', 'module']))
                <a href="{{ route('admin.errors.index', ['view' => $viewMode]) }}" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 text-xs font-semibold transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- MODULE QUICK-TABS & VIEW SWITCHER BAR -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-2 rounded-2xl bg-white/[0.02] border border-white/5">
        <!-- Quick Module Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none text-xs">
            <a href="{{ route('admin.errors.index', array_merge(request()->except('module'), ['view' => $viewMode])) }}" 
               class="px-3 py-1.5 rounded-xl font-bold transition-all whitespace-nowrap {{ !request('module') ? 'bg-white/15 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                All Modules ({{ $stats['total'] }})
            </a>
            @foreach($modulesDef as $mk => $m)
                @php
                    $mTotal = $moduleStats[$mk]['total'] ?? 0;
                    $isCur = request('module') === $mk;
                @endphp
                <a href="{{ route('admin.errors.index', array_merge(request()->query(), ['module' => $mk, 'view' => $viewMode])) }}" 
                   class="px-3 py-1.5 rounded-xl font-bold transition-all flex items-center gap-1.5 whitespace-nowrap {{ $isCur ? 'bg-rose-500 text-white shadow-md shadow-rose-500/20' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span>{{ $m['icon'] }}</span>
                    <span>{{ $m['short_name'] }}</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $isCur ? 'bg-white/20 text-white' : 'bg-white/5 text-slate-400' }} font-mono">{{ $mTotal }}</span>
                </a>
            @endforeach
        </div>

        <!-- View Switcher (Grouped by Module vs Flat Stream) -->
        <div class="flex items-center gap-1 bg-[#12131A] p-1 rounded-xl border border-white/10 shrink-0 self-start sm:self-auto">
            <a href="{{ route('admin.errors.index', array_merge(request()->query(), ['view' => 'grouped'])) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $viewMode === 'grouped' ? 'bg-rose-500 text-white shadow-sm' : 'text-slate-400 hover:text-white' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Group by Module
            </a>
            <a href="{{ route('admin.errors.index', array_merge(request()->query(), ['view' => 'flat'])) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $viewMode === 'flat' ? 'bg-rose-500 text-white shadow-sm' : 'text-slate-400 hover:text-white' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                Flat Stream
            </a>
        </div>
    </div>

    <!-- MAIN ERROR PRESENTATION: GROUPED BY MODULE vs FLAT STREAM -->
    @if($viewMode === 'grouped')
        <!-- GROUPED BY MODULE VIEW -->
        <div class="space-y-6">
            @php
                // Modules to display: if activeModule is set, only show that one; else show all modules in definition
                $modulesToRender = request('module') 
                    ? [request('module') => $modulesDef[request('module')] ?? $modulesDef['core']]
                    : $modulesDef;
            @endphp

            @foreach($modulesToRender as $modKey => $modInfo)
                @php
                    $modErrors = $groupedErrors->get($modKey, collect());
                    $mStats = $moduleStats[$modKey] ?? ['total' => 0, 'unresolved' => 0, 'fatal' => 0, 'status_color' => 'emerald', 'status_label' => 'Healthy'];
                @endphp

                <div class="glass-panel p-6 rounded-3xl space-y-4 border {{ $modErrors->isNotEmpty() ? 'border-white/10' : 'border-white/5 opacity-70' }}" id="module-section-{{ $modKey }}">
                    <!-- Module Section Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-white/5 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl p-2.5 rounded-2xl bg-white/5 border border-white/10">{{ $modInfo['icon'] }}</span>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-lg font-black text-white tracking-tight">{{ $modInfo['name'] }}</h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $modInfo['badge_bg'] }} {{ $modInfo['badge_text'] }} border {{ $modInfo['badge_border'] }}">
                                        {{ $mStats['total'] }} {{ Str::plural('issue', $mStats['total']) }}
                                    </span>
                                    @if($mStats['unresolved'] > 0)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                            {{ $mStats['unresolved'] }} Open
                                        </span>
                                    @endif
                                    @if($mStats['fatal'] > 0)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                            🔥 {{ $mStats['fatal'] }} Fatal
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400 mt-1">{{ $modInfo['description'] }}</p>
                            </div>
                        </div>

                        <!-- Module Actions -->
                        <div class="flex items-center gap-2 self-start sm:self-auto">
                            @if($mStats['unresolved'] > 0)
                                <form action="{{ route('admin.errors.resolveModule', $modKey) }}" method="POST" onsubmit="return confirm('Mark all {{ $mStats['unresolved'] }} open issues in {{ $modInfo['name'] }} as resolved?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 text-xs font-bold border border-emerald-500/30 transition-colors flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Resolve {{ $mStats['unresolved'] }} in Module
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('admin.errors.export', array_merge(request()->query(), ['module' => $modKey, 'format' => 'markdown'])) }}" 
                               class="px-2.5 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-semibold border border-white/10 transition-colors flex items-center gap-1"
                               title="Export {{ $modInfo['name'] }} debug report">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Export
                            </a>
                        </div>
                    </div>

                    <!-- Module Table -->
                    @if($modErrors->isEmpty())
                        <div class="py-8 text-center text-slate-400 text-xs">
                            <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-400 mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="font-bold text-slate-300">Module Running Cleanly</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">No matching error records logged for {{ $modInfo['name'] }}.</div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-white/10 text-slate-400 uppercase tracking-wider text-[11px]">
                                        <th class="pb-3 font-semibold">Severity & Screen</th>
                                        <th class="pb-3 font-semibold">Error Message & Diagnostics</th>
                                        <th class="pb-3 font-semibold">Device & Environment</th>
                                        <th class="pb-3 font-semibold">Status</th>
                                        <th class="pb-3 font-semibold">Reported At</th>
                                        <th class="pb-3 font-semibold text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @foreach($modErrors as $error)
                                        @include('admin.partials.error_row', ['error' => $error, 'modulesDef' => $modulesDef])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <!-- FLAT STREAM VIEW -->
        <div class="glass-panel p-6 rounded-3xl space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-white/10 text-slate-400 uppercase tracking-wider text-[11px]">
                            <th class="pb-3.5 font-semibold">Severity & Module</th>
                            <th class="pb-3.5 font-semibold">Error Message & Diagnostics</th>
                            <th class="pb-3.5 font-semibold">Device & Environment</th>
                            <th class="pb-3.5 font-semibold">Status</th>
                            <th class="pb-3.5 font-semibold">Reported At</th>
                            <th class="pb-3.5 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($errors as $error)
                            @include('admin.partials.error_row', ['error' => $error, 'modulesDef' => $modulesDef])
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
    @endif
</div>

<!-- SINGLE UNIVERSAL INSPECT ERROR MODAL (Native Vanilla JS) -->
<div id="universalInspectModal" 
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4 text-left">
    <div class="glass-panel p-6 sm:p-8 rounded-3xl max-w-3xl w-full border border-white/10 shadow-2xl max-h-[92vh] overflow-y-auto space-y-5">
        
        <!-- Modal Top Bar -->
        <div class="flex items-start justify-between gap-4 border-b border-white/10 pb-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span id="modalSeverityBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase"></span>
                    <span id="modalModuleBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"></span>
                    <span id="modalScreenBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-mono bg-white/10 text-slate-300"></span>
                    <span id="modalIssueId" class="text-xs text-slate-400 font-mono"></span>
                </div>
                <h3 id="modalErrorMessage" class="text-lg sm:text-xl font-bold text-white mt-2 break-words leading-snug"></h3>
            </div>
            <button type="button" onclick="closeInspectModal()" class="text-slate-400 hover:text-white text-3xl font-bold p-1 leading-none">&times;</button>
        </div>

        <!-- Device & Environment Card -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-2xl bg-white/5 border border-white/5 text-xs">
            <div>
                <div class="text-[10px] text-slate-400 uppercase font-semibold">User & Device ID</div>
                <div id="modalUser" class="font-bold text-white mt-0.5 truncate"></div>
                <div id="modalDeviceId" class="font-mono text-[10px] text-slate-500 truncate"></div>
            </div>
            <div>
                <div class="text-[10px] text-slate-400 uppercase font-semibold">Platform & OS</div>
                <div id="modalPlatform" class="font-bold text-slate-200 mt-0.5 uppercase"></div>
                <div id="modalOs" class="text-[10px] text-slate-400 truncate"></div>
            </div>
            <div>
                <div class="text-[10px] text-slate-400 uppercase font-semibold">App Version & IP</div>
                <div id="modalAppVersion" class="font-bold text-slate-200 mt-0.5"></div>
                <div id="modalIp" class="font-mono text-[10px] text-slate-400 truncate"></div>
            </div>
            <div>
                <div class="text-[10px] text-slate-400 uppercase font-semibold">Location & Time</div>
                <div id="modalLocation" class="font-bold text-white mt-0.5 flex items-center gap-1"></div>
                <div id="modalTime" class="text-[10px] text-slate-400"></div>
            </div>
        </div>

        <!-- Stack Trace Section -->
        <div id="modalStackTraceSection" class="space-y-2">
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    Stack Trace
                </div>
                <button type="button" 
                        id="modalCopyTraceBtn"
                        class="text-[11px] font-semibold text-sky-400 hover:text-sky-300 flex items-center gap-1 bg-sky-500/10 px-2 py-1 rounded-lg border border-sky-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    <span id="modalCopyTraceBtnText">Copy Trace</span>
                </button>
            </div>
            <pre id="modalStackTrace" class="p-4 rounded-2xl bg-[#0B0C10] border border-white/10 text-emerald-400 font-mono text-[11px] overflow-x-auto max-h-48 whitespace-pre-wrap leading-relaxed"></pre>
        </div>

        <!-- Metadata Section -->
        <div id="modalMetadataSection" class="space-y-2">
            <div class="text-xs font-bold text-slate-300 uppercase tracking-wider">Context Metadata</div>
            <pre id="modalMetadata" class="p-4 rounded-2xl bg-[#0B0C10] border border-white/10 text-amber-300 font-mono text-[11px] overflow-x-auto max-h-36 whitespace-pre-wrap"></pre>
        </div>

        <!-- Generated AI Debug Prompt Box -->
        <div class="space-y-2 pt-2 border-t border-white/10">
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold text-purple-300 uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    AI Debug Prompt Preview (Ready for Claude, ChatGPT, Gemini)
                </div>
                <button type="button" 
                        id="modalPromptQuickCopy" 
                        onclick="copyModalPrompt()" 
                        class="text-[11px] font-bold text-purple-300 hover:text-white flex items-center gap-1.5 bg-purple-500/20 hover:bg-purple-500/30 px-3 py-1 rounded-xl border border-purple-500/40 transition-all">
                    <svg class="w-3.5 h-3.5 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    <span id="modalPromptQuickCopyText">Copy Prompt</span>
                </button>
            </div>
            <div class="relative">
                <textarea id="modalPromptTextarea" 
                          readonly 
                          rows="6" 
                          class="w-full p-3.5 rounded-2xl bg-[#090A0F] border border-purple-500/30 text-purple-200 font-mono text-[11px] focus:ring-1 focus:ring-purple-400 outline-none leading-relaxed cursor-pointer select-all"
                          onclick="this.select()"
                          title="Click to select all text"></textarea>
                <div class="text-[10px] text-slate-500 mt-1 flex items-center justify-between">
                    <span>💡 Tip: Click inside box to select all text, or click "Copy Prompt".</span>
                    <span class="text-purple-400/80 font-mono">Markdown Format</span>
                </div>
            </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="flex items-center justify-between gap-3 pt-4 border-t border-white/10 flex-wrap">
            <div class="flex items-center gap-2">
                <button type="button" 
                        id="modalCopyPromptBtn"
                        onclick="copyModalPrompt()"
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white border border-purple-400/30 text-xs font-bold flex items-center gap-2 transition-all shadow-lg shadow-purple-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    <span id="modalCopyPromptBtnText">Copy Debug Prompt for AI</span>
                </button>

                <button type="button" 
                        id="modalCopyRawMsgBtn"
                        onclick="copyRawErrorMessage()"
                        class="px-3 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white border border-white/10 text-xs font-semibold flex items-center gap-1.5 transition-all">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span id="modalCopyRawMsgBtnText">Copy Raw Error</span>
                </button>
            </div>

            <div class="flex items-center gap-2">
                <form id="modalResolveForm" method="POST">
                    @csrf
                    <button type="submit" id="modalResolveBtn" class="px-4 py-2.5 rounded-xl text-white text-xs font-bold transition-all shadow-md"></button>
                </form>

                <button type="button" onclick="closeInspectModal()" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-white text-xs font-bold transition-all">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentModalErrorData = null;

    function showToast(message) {
        const toast = document.getElementById('globalToast');
        const msgSpan = document.getElementById('globalToastMessage');
        if (!toast || !msgSpan) return;

        msgSpan.innerText = message;
        toast.classList.remove('hidden');
        toast.classList.add('flex');

        setTimeout(() => {
            toast.classList.add('hidden');
            toast.classList.remove('flex');
        }, 3000);
    }

    function toggleExportDropdown() {
        const menu = document.getElementById('exportDropdownMenu');
        if (menu) menu.classList.toggle('hidden');
    }

    document.addEventListener('click', function(e) {
        const btn = document.getElementById('exportDropdownBtn');
        const menu = document.getElementById('exportDropdownMenu');
        if (btn && menu && !btn.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });

    function openInspectModal(data) {
        currentModalErrorData = data;
        const modal = document.getElementById('universalInspectModal');
        if (!modal) return;

        // Set badges
        const sevBadge = document.getElementById('modalSeverityBadge');
        if (sevBadge) {
            sevBadge.innerText = data.severity;
            if (data.severity === 'FATAL') {
                sevBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-500/20 text-rose-400 border border-rose-500/30';
            } else if (data.severity === 'ERROR') {
                sevBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30';
            } else {
                sevBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-yellow-500/20 text-yellow-300 border border-yellow-500/30';
            }
        }

        // Set module badge
        const modBadge = document.getElementById('modalModuleBadge');
        if (modBadge) {
            modBadge.innerText = (data.module_icon || '📦') + ' ' + (data.module_name || data.module || 'Core');
            modBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold ' + (data.module_bg || 'bg-white/10') + ' ' + (data.module_text || 'text-slate-300') + ' border ' + (data.module_border || 'border-white/10');
        }

        const screenBadge = document.getElementById('modalScreenBadge');
        if (screenBadge) screenBadge.innerText = '📺 ' + (data.screen || 'global');

        const issueId = document.getElementById('modalIssueId');
        if (issueId) issueId.innerText = '#' + data.id + ' • ' + data.error_type;

        const errorMsg = document.getElementById('modalErrorMessage');
        if (errorMsg) errorMsg.innerText = data.error_message;

        // Device card
        document.getElementById('modalUser').innerText = data.username || 'Anonymous Device';
        document.getElementById('modalDeviceId').innerText = data.device_id;
        document.getElementById('modalPlatform').innerText = data.platform;
        document.getElementById('modalOs').innerText = data.os;
        document.getElementById('modalAppVersion').innerText = 'v' + data.app_version;
        document.getElementById('modalIp').innerText = data.ip;
        document.getElementById('modalLocation').innerText = (data.country_flag || '🌐') + ' ' + (data.country || 'Global');
        document.getElementById('modalTime').innerText = data.time;

        // Stack Trace
        const traceSection = document.getElementById('modalStackTraceSection');
        const tracePre = document.getElementById('modalStackTrace');
        if (data.stack_trace && data.stack_trace.trim().length > 0) {
            tracePre.innerText = data.stack_trace;
            traceSection.classList.remove('hidden');
        } else {
            traceSection.classList.add('hidden');
        }

        // Metadata
        const metaSection = document.getElementById('modalMetadataSection');
        const metaPre = document.getElementById('modalMetadata');
        if (data.metadata && Object.keys(data.metadata).length > 0) {
            metaPre.innerText = typeof data.metadata === 'object' ? JSON.stringify(data.metadata, null, 2) : String(data.metadata);
            metaSection.classList.remove('hidden');
        } else {
            metaSection.classList.add('hidden');
        }

        // AI Debug Prompt
        const promptArea = document.getElementById('modalPromptTextarea');
        if (promptArea) {
            promptArea.value = data.prompt;
        }

        // Copy trace button handler
        const copyTraceBtn = document.getElementById('modalCopyTraceBtn');
        if (copyTraceBtn) {
            copyTraceBtn.onclick = function() {
                if (data.stack_trace) {
                    navigator.clipboard.writeText(data.stack_trace);
                    document.getElementById('modalCopyTraceBtnText').innerText = 'Copied!';
                    showToast('Stack trace copied to clipboard');
                    setTimeout(() => {
                        const t = document.getElementById('modalCopyTraceBtnText');
                        if (t) t.innerText = 'Copy Trace';
                    }, 2000);
                }
            };
        }

        // Resolve Form
        const resolveForm = document.getElementById('modalResolveForm');
        const resolveBtn = document.getElementById('modalResolveBtn');
        if (resolveForm && resolveBtn) {
            resolveForm.action = data.resolve_url;
            if (data.resolved) {
                resolveBtn.innerText = 'Reopen Issue';
                resolveBtn.className = 'px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shadow-md shadow-amber-500/25';
            } else {
                resolveBtn.innerText = 'Mark as Resolved';
                resolveBtn.className = 'px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition-all shadow-md shadow-emerald-500/25';
            }
        }

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeInspectModal() {
        const modal = document.getElementById('universalInspectModal');
        if (modal) modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function copyModalPrompt() {
        if (!currentModalErrorData || !currentModalErrorData.prompt) return;
        navigator.clipboard.writeText(currentModalErrorData.prompt).then(() => {
            const btnText = document.getElementById('modalCopyPromptBtnText');
            const quickText = document.getElementById('modalPromptQuickCopyText');
            if (btnText) btnText.innerText = 'Prompt Copied to Clipboard! ✓';
            if (quickText) quickText.innerText = 'Copied! ✓';
            showToast('AI Debug Prompt copied to clipboard! Ready to paste into Claude/ChatGPT');

            setTimeout(() => {
                if (btnText) btnText.innerText = 'Copy Debug Prompt for AI';
                if (quickText) quickText.innerText = 'Copy Prompt';
            }, 2500);
        });
    }

    function copyRawErrorMessage() {
        if (!currentModalErrorData || !currentModalErrorData.error_message) return;
        navigator.clipboard.writeText(currentModalErrorData.error_message).then(() => {
            const btnText = document.getElementById('modalCopyRawMsgBtnText');
            if (btnText) btnText.innerText = 'Error Copied! ✓';
            showToast('Raw error message copied to clipboard');

            setTimeout(() => {
                if (btnText) btnText.innerText = 'Copy Raw Error';
            }, 2000);
        });
    }

    function copyTableRowPrompt(btn, promptText) {
        navigator.clipboard.writeText(promptText).then(() => {
            const label = btn.querySelector('.copy-label');
            const orig = label ? label.innerText : 'Copy AI Prompt';
            if (label) label.innerText = 'Copied! ✓';
            showToast('Debug Prompt copied! Paste directly into AI for immediate resolution');

            setTimeout(() => {
                if (label) label.innerText = orig;
            }, 2000);
        });
    }

    // Close modal on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeInspectModal();
    });
</script>
@endsection