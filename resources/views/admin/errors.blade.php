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
                Real-time error logs and stack traces captured from client devices. Inspect full diagnostics and export structured debug reports directly for AI or developers.
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
                            $devId = $error->device_id ?: ($dev?->device_id ?? 'unknown');
                            $devUser = $dev?->display_name ?: ($dev?->username ?? 'Anonymous Device');
                            $platform = strtoupper($error->platform ?: ($dev?->platform ?? 'ANDROID'));
                            $os = $dev?->os_version ?: 'OS';
                            $appVer = $error->app_version ?: ($dev?->app_version ?? '1.0.0');
                            $country = $dev?->country ?: 'Global';
                            $countryFlag = $dev?->country_flag ?: '🌐';
                            $ip = $dev?->ip_address ?: '127.0.0.1';
                            $timeStr = $error->created_at ? $error->created_at->format('M d, Y H:i:s') : 'Unknown';

                            // Format structured context metadata
                            $metadataStr = '';
                            $metaObj = [];
                            if (!empty($error->metadata)) {
                                if (is_array($error->metadata)) {
                                    $metaObj = $error->metadata;
                                } elseif (is_object($error->metadata)) {
                                    $metaObj = (array)$error->metadata;
                                } else {
                                    $decoded = json_decode($error->metadata, true);
                                    $metaObj = is_array($decoded) ? $decoded : [];
                                }
                                $metadataStr = !empty($metaObj) 
                                    ? json_encode($metaObj, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                                    : (string)$error->metadata;
                            }

                            // Extract media details for crystal-clear debug understanding
                            $mediaDetails = [];
                            if (!empty($metaObj['title'])) $mediaDetails[] = "- **Media Title**: " . $metaObj['title'];
                            if (!empty($metaObj['mediaType'])) $mediaDetails[] = "- **Media Type**: " . strtoupper($metaObj['mediaType']);
                            if (isset($metaObj['season'])) $mediaDetails[] = "- **Season**: " . $metaObj['season'];
                            if (isset($metaObj['episode'])) $mediaDetails[] = "- **Episode**: " . $metaObj['episode'];
                            if (!empty($metaObj['mediaId'])) $mediaDetails[] = "- **Media ID**: " . $metaObj['mediaId'];
                            if (isset($metaObj['isLocalMedia'])) $mediaDetails[] = "- **Storage**: " . ($metaObj['isLocalMedia'] ? 'Local Download (Offline Vault)' : 'Remote Online Streaming');

                            $mediaContextBlock = !empty($mediaDetails) 
                                ? implode("\n", $mediaDetails) . "\n" 
                                : "- *No media context recorded*\n";

                            $httpCode = null;
                            if (preg_match('/Response code:\s*(\d+)/i', $error->error_message, $mMatches)) {
                                $httpCode = $mMatches[1];
                            }

                            $promptText = "### 🐞 MovieCat Mobile App Debug Report\n\n" .
                                "**Summary**: A client runtime exception was captured on the MovieCat mobile application. Please analyze the root cause and provide clear, step-by-step resolution instructions for both mobile client (React Native / Expo / ExoPlayer) and backend API / streaming server.\n\n" .
                                "#### 1. Issue Overview\n" .
                                "- **Issue ID**: #" . $error->id . " (" . $error->error_type . ")\n" .
                                "- **Severity**: " . strtoupper($error->severity) . "\n" .
                                "- **Screen**: `" . ($error->screen ?: 'global') . "`\n" .
                                "- **Reported At**: " . $timeStr . " UTC\n\n" .
                                "#### 2. Error Message & Diagnostics\n" .
                                "```\n" . $error->error_message . "\n```\n\n" .
                                "#### 3. Media & Playback Context\n" .
                                $mediaContextBlock . "\n" .
                                "#### 4. Client & Environment Telemetry\n" .
                                "- **Device ID**: `" . $devId . "` (" . $devUser . ")\n" .
                                "- **Platform & OS**: " . $platform . " " . $os . "\n" .
                                "- **App Version**: v" . $appVer . "\n" .
                                "- **Client IP**: `" . $ip . "`\n" .
                                "- **Location**: " . $countryFlag . " " . $country . "\n\n" .
                                "#### 5. Context Metadata (Raw JSON)\n" .
                                "```json\n" . (!empty($metadataStr) ? $metadataStr : "{}\n") . "\n```\n\n" .
                                (!empty($error->stack_trace) ? "#### 6. Stack Trace\n```\n" . $error->stack_trace . "\n```\n\n" : "") .
                                "#### " . (!empty($error->stack_trace) ? "7" : "6") . ". Questions for AI Assistant\n" .
                                "1. **Root Cause**: Why did this exception occur on the `" . ($error->screen ?: 'global') . "` screen?" . ($httpCode ? " What does HTTP Response Code " . $httpCode . " indicate in video streaming / CDN (e.g. Cloudflare / ExoPlayer rate limiting or blocked IP)?" : "") . "\n" .
                                "2. **Client-Side Resilience**: What error recovery, retry strategy, or player fallback logic should be implemented in the mobile app (`app/(tabs)/player.tsx` or Video Player component) so playback recovers gracefully without interrupting the user experience?\n" .
                                "3. **Backend / CDN Fix**: What server-side headers, Cloudflare rules, CORS configuration, or CDN token parameters should be updated to resolve this issue?\n";

                            $errorJsonData = [
                                'id' => $error->id,
                                'severity' => strtoupper($error->severity),
                                'screen' => $error->screen ?: 'global',
                                'error_type' => $error->error_type,
                                'error_message' => $error->error_message,
                                'device_id' => $devId,
                                'username' => $devUser,
                                'platform' => $platform,
                                'os' => $os,
                                'app_version' => $appVer,
                                'country' => $country,
                                'country_flag' => $countryFlag,
                                'ip' => $ip,
                                'time' => $timeStr,
                                'resolved' => (bool)$error->resolved,
                                'stack_trace' => $error->stack_trace,
                                'metadata' => $metaObj ?: $error->metadata,
                                'prompt' => $promptText,
                                'resolve_url' => route('admin.errors.resolve', $error->id),
                            ];
                        @endphp
                        <tr class="hover:bg-white/[0.02] transition-colors" id="error-row-{{ $error->id }}">
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
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Resolved
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-pulse"></span>
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
                                <!-- Inspect Button (Native Vanilla JS) -->
                                <button type="button" 
                                        onclick="openInspectModal({{ json_encode($errorJsonData) }})"
                                        class="px-2.5 py-1 rounded-lg bg-sky-500/15 hover:bg-sky-500/25 text-sky-300 border border-sky-500/30 text-[11px] font-bold transition-all inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Inspect
                                </button>

                                <!-- 1-Click Copy AI Prompt (with instant checkmark feedback) -->
                                <button type="button" 
                                        onclick="copyRowPrompt(this, {{ json_encode($promptText) }})"
                                        title="Copy formatted prompt for AI assistant (Claude/GPT/Gemini)"
                                        class="p-1.5 rounded-lg bg-purple-500/15 hover:bg-purple-500/25 text-purple-300 border border-purple-500/30 transition-all inline-flex items-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                </button>

                                <!-- Resolve Toggle -->
                                <form action="{{ route('admin.errors.resolve', $error->id) }}" method="POST" class="inline">
                                    @csrf
                                    @if($error->resolved)
                                        <button type="submit" class="px-2 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white text-[11px] font-semibold border border-white/10 transition-colors">
                                            Reopen
                                        </button>
                                    @else
                                        <button type="submit" class="px-2 py-1 rounded-lg bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-400 border border-emerald-500/30 text-[11px] font-bold transition-colors">
                                            Resolve
                                        </button>
                                    @endif
                                </form>

                                <!-- Delete -->
                                <form action="{{ route('admin.errors.delete', $error->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this error log record permanently?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-400 hover:bg-white/5 transition-colors" title="Delete Log">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
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

<!-- SINGLE UNIVERSAL INSPECT ERROR MODAL (Native Vanilla JS) -->
<div id="universalInspectModal" 
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4 text-left">
    <div class="glass-panel p-6 sm:p-8 rounded-3xl max-w-3xl w-full border border-white/10 shadow-2xl max-h-[92vh] overflow-y-auto space-y-5">
        
        <!-- Modal Top Bar -->
        <div class="flex items-start justify-between gap-4 border-b border-white/10 pb-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span id="modalSeverityBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase"></span>
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

        <!-- Generated AI Debug Prompt Box (Instant Visual Copy & Preview) -->
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
                <button type="button" onclick="closeInspectModal()" class="px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-semibold">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentInspectData = null;

function openInspectModal(data) {
    currentInspectData = data;
    const modal = document.getElementById('universalInspectModal');
    if (!modal) return;

    // Severity badge styling
    const sevBadge = document.getElementById('modalSeverityBadge');
    if (sevBadge) {
        sevBadge.textContent = data.severity;
        if (data.severity === 'FATAL') {
            sevBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-500/20 text-rose-400 border border-rose-500/40';
        } else if (data.severity === 'ERROR') {
            sevBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/40';
        } else {
            sevBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-yellow-500/20 text-yellow-300 border border-yellow-500/40';
        }
    }

    document.getElementById('modalScreenBadge').textContent = 'Screen: ' + data.screen;
    document.getElementById('modalIssueId').textContent = 'Issue #' + data.id + ' (' + data.error_type + ')';
    document.getElementById('modalErrorMessage').textContent = data.error_message;

    document.getElementById('modalUser').textContent = data.username;
    document.getElementById('modalDeviceId').textContent = data.device_id;
    document.getElementById('modalPlatform').textContent = data.platform;
    document.getElementById('modalOs').textContent = data.os;
    document.getElementById('modalAppVersion').textContent = 'v' + data.app_version;
    document.getElementById('modalIp').textContent = data.ip;
    document.getElementById('modalLocation').textContent = (data.country_flag || '🌐') + ' ' + data.country;
    document.getElementById('modalTime').textContent = data.time;

    // Stack Trace
    const traceSection = document.getElementById('modalStackTraceSection');
    const traceElem = document.getElementById('modalStackTrace');
    if (data.stack_trace && data.stack_trace.trim().length > 0) {
        traceSection.classList.remove('hidden');
        traceElem.textContent = data.stack_trace;
        document.getElementById('modalCopyTraceBtn').onclick = function() {
            copyTextToClipboard(data.stack_trace, function() {
                showToast('Stack trace copied to clipboard!');
                const txt = document.getElementById('modalCopyTraceBtnText');
                if (txt) {
                    txt.textContent = '✓ Copied!';
                    setTimeout(() => txt.textContent = 'Copy Trace', 2000);
                }
            });
        };
    } else {
        traceSection.classList.add('hidden');
    }

    // Metadata
    const metaSection = document.getElementById('modalMetadataSection');
    const metaElem = document.getElementById('modalMetadata');
    if (data.metadata) {
        metaSection.classList.remove('hidden');
        metaElem.textContent = typeof data.metadata === 'object' ? JSON.stringify(data.metadata, null, 2) : data.metadata;
    } else {
        metaSection.classList.add('hidden');
    }

    // AI Prompt Preview Textarea
    const promptTextarea = document.getElementById('modalPromptTextarea');
    if (promptTextarea) {
        promptTextarea.value = data.prompt || '';
    }

    // Resolve Form Button
    const resolveForm = document.getElementById('modalResolveForm');
    const resolveBtn = document.getElementById('modalResolveBtn');
    resolveForm.action = data.resolve_url;
    if (data.resolved) {
        resolveBtn.textContent = 'Reopen Issue';
        resolveBtn.className = 'px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white text-xs font-bold transition-all';
    } else {
        resolveBtn.textContent = 'Mark as Resolved';
        resolveBtn.className = 'px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition-all shadow-md';
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeInspectModal() {
    const modal = document.getElementById('universalInspectModal');
    if (modal) {
        modal.classList.add('hidden');
    }
    document.body.style.overflow = 'auto';
}

function toggleExportDropdown() {
    const menu = document.getElementById('exportDropdownMenu');
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

// Copy the full AI Debug Prompt from the modal with multi-element visual feedback
function copyModalPrompt() {
    if (!currentInspectData || !currentInspectData.prompt) {
        showToast('No prompt data available');
        return;
    }

    const promptText = currentInspectData.prompt;
    
    // Select the visible textarea for visual confirmation
    const promptTextarea = document.getElementById('modalPromptTextarea');
    if (promptTextarea) {
        promptTextarea.focus();
        promptTextarea.select();
    }

    copyTextToClipboard(promptText, function() {
        showToast('✓ AI Debug Prompt copied to clipboard!');

        // Update footer button
        const mainBtnText = document.getElementById('modalCopyPromptBtnText');
        const mainBtn = document.getElementById('modalCopyPromptBtn');
        if (mainBtnText && mainBtn) {
            mainBtnText.textContent = '✓ Copied to Clipboard!';
            mainBtn.classList.add('ring-2', 'ring-emerald-400');
            setTimeout(function() {
                mainBtnText.textContent = 'Copy Debug Prompt for AI';
                mainBtn.classList.remove('ring-2', 'ring-emerald-400');
            }, 2500);
        }

        // Update top-right quick copy button
        const quickText = document.getElementById('modalPromptQuickCopyText');
        if (quickText) {
            quickText.textContent = '✓ Copied!';
            setTimeout(function() {
                quickText.textContent = 'Copy Prompt';
            }, 2500);
        }
    }, function(err) {
        showToast('Press Cmd+C / Ctrl+C to copy selected text');
    });
}

// Copy raw error message
function copyRawErrorMessage() {
    if (!currentInspectData) return;
    const msg = currentInspectData.error_message || '';
    copyTextToClipboard(msg, function() {
        showToast('✓ Error message copied to clipboard!');
        const btnText = document.getElementById('modalCopyRawMsgBtnText');
        if (btnText) {
            btnText.textContent = '✓ Copied!';
            setTimeout(() => btnText.textContent = 'Copy Raw Error', 2000);
        }
    });
}

// Copy prompt from table row with icon animation
function copyRowPrompt(btn, promptText) {
    const originalHtml = btn.innerHTML;
    copyTextToClipboard(promptText, function() {
        showToast('✓ AI debug prompt copied to clipboard!');
        btn.innerHTML = `<svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`;
        btn.classList.add('bg-emerald-500/20', 'border-emerald-500/40');
        setTimeout(function() {
            btn.innerHTML = originalHtml;
            btn.classList.remove('bg-emerald-500/20', 'border-emerald-500/40');
        }, 2500);
    }, function() {
        showToast('Press Cmd+C / Ctrl+C to copy');
    });
}

// Bulletproof multi-strategy clipboard copier
function copyTextToClipboard(text, onSuccess, onError) {
    if (!text) {
        if (onError) onError('No text provided');
        return;
    }

    // Strategy 1: Async Clipboard API (Modern Secure Contexts)
    if (navigator.clipboard && window.isSecureContext && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            if (onSuccess) onSuccess();
        }).catch(function() {
            fallbackExecCopy(text, onSuccess, onError);
        });
        return;
    }

    // Strategy 2: execCommand fallback (Non-HTTPS / Legacy / Sandboxed)
    fallbackExecCopy(text, onSuccess, onError);
}

function fallbackExecCopy(text, onSuccess, onError) {
    // If modal prompt textarea is available and matching, copy directly from it
    const promptTextarea = document.getElementById('modalPromptTextarea');
    if (promptTextarea && promptTextarea.value === text) {
        promptTextarea.focus();
        promptTextarea.select();
        promptTextarea.setSelectionRange(0, 999999);
        try {
            const ok = document.execCommand('copy');
            if (ok) {
                if (onSuccess) onSuccess();
                return;
            }
        } catch (e) {}
    }

    // Fallback: Create unconstrained temporary textarea in DOM
    const el = document.createElement('textarea');
    el.value = text;
    el.style.position = 'fixed';
    el.style.top = '10px';
    el.style.left = '10px';
    el.style.width = '100px';
    el.style.height = '100px';
    el.style.padding = '0';
    el.style.border = 'none';
    el.style.outline = 'none';
    el.style.boxShadow = 'none';
    el.style.background = 'transparent';
    el.style.opacity = '0.01';
    el.style.zIndex = '99999';
    document.body.appendChild(el);
    el.focus();
    el.select();
    el.setSelectionRange(0, 999999);

    let success = false;
    try {
        success = document.execCommand('copy');
    } catch (err) {
        success = false;
    }
    document.body.removeChild(el);

    if (success) {
        if (onSuccess) onSuccess();
    } else {
        if (onError) onError('Browser prevented automated copy');
    }
}

function showToast(msg) {
    const toast = document.getElementById('globalToast');
    const toastMsg = document.getElementById('globalToastMessage');
    if (toast && toastMsg) {
        toastMsg.textContent = msg;
        toast.classList.remove('hidden');
        setTimeout(function() {
            toast.classList.add('hidden');
        }, 3000);
    }
}

// Close modal when clicking backdrop
document.addEventListener('click', function(e) {
    const modal = document.getElementById('universalInspectModal');
    if (modal && !modal.classList.contains('hidden') && e.target === modal) {
        closeInspectModal();
    }

    // Close export dropdown when clicking outside
    const exportBtn = document.getElementById('exportDropdownBtn');
    const exportMenu = document.getElementById('exportDropdownMenu');
    if (exportMenu && !exportMenu.classList.contains('hidden')) {
        if (exportBtn && !exportBtn.contains(e.target) && !exportMenu.contains(e.target)) {
            exportMenu.classList.add('hidden');
        }
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeInspectModal();
        const exportMenu = document.getElementById('exportDropdownMenu');
        if (exportMenu) exportMenu.classList.add('hidden');
    }
});
</script>
@endsection
