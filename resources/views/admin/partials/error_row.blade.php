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

    // Module info
    $modKey = $error->module;
    $modMeta = $modulesDef[$modKey] ?? ($modulesDef['core'] ?? [
        'name' => 'Core Engine & Network',
        'short_name' => 'Core',
        'icon' => '⚙️',
        'badge_bg' => 'bg-slate-500/10',
        'badge_text' => 'text-slate-400',
        'badge_border' => 'border-slate-500/20',
    ]);

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

    // Extract media details if present
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
        "- **App Module**: " . $modMeta['icon'] . " " . $modMeta['name'] . "\n" .
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
        "1. **Module Root Cause**: Why did this exception occur in the " . $modMeta['name'] . " module (`" . ($error->screen ?: 'global') . "` screen)?" . ($httpCode ? " What does HTTP Response Code " . $httpCode . " indicate in video streaming / CDN (e.g. Cloudflare / ExoPlayer rate limiting or blocked IP)?" : "") . "\n" .
        "2. **Client-Side Resilience**: What error recovery, retry strategy, or player fallback logic should be implemented in the mobile app (`app/(tabs)/player.tsx` or related subsystem) so playback recovers gracefully without interrupting the user experience?\n" .
        "3. **Backend / Server Fix**: What server-side headers, Cloudflare rules, CORS configuration, or database parameters should be updated to resolve this issue?\n";

    $errorJsonData = [
        'id' => $error->id,
        'severity' => strtoupper($error->severity),
        'module' => $modKey,
        'module_name' => $modMeta['short_name'],
        'module_icon' => $modMeta['icon'],
        'module_bg' => $modMeta['badge_bg'],
        'module_text' => $modMeta['badge_text'],
        'module_border' => $modMeta['badge_border'],
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
    <!-- Severity & Module -->
    <td class="py-4 align-top">
        <div class="flex flex-col gap-1.5 items-start">
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
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">
                        WARN
                    </span>
                @endif

                <a href="{{ route('admin.errors.index', array_merge(request()->query(), ['module' => $modKey])) }}" 
                   class="px-2 py-0.5 rounded text-[10px] font-bold {{ $modMeta['badge_bg'] }} {{ $modMeta['badge_text'] }} border {{ $modMeta['badge_border'] }} hover:opacity-80 transition-opacity flex items-center gap-1"
                   title="Module: {{ $modMeta['name'] }}">
                    <span>{{ $modMeta['icon'] }}</span>
                    <span>{{ $modMeta['short_name'] }}</span>
                </a>
            </div>

            <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-white/5 text-slate-300 border border-white/10">
                📺 {{ $error->screen ?: 'global' }}
            </span>
        </div>
    </td>

    <!-- Error Message & Diagnostics -->
    <td class="py-4 align-top max-w-md">
        <div class="font-bold text-white break-words text-sm leading-snug">{{ $error->error_message }}</div>
        
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

    <!-- Device & Environment -->
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
        <!-- Inspect Button -->
        <button type="button" 
                onclick="openInspectModal({{ json_encode($errorJsonData) }})"
                class="px-2.5 py-1 rounded-lg bg-sky-500/15 hover:bg-sky-500/25 text-sky-300 border border-sky-500/30 text-[11px] font-bold transition-all inline-flex items-center gap-1">
            <svg class="w-3 h-3 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            Inspect
        </button>

        <!-- 1-Click Copy AI Prompt -->
        <button type="button" 
                onclick="copyTableRowPrompt(this, {{ json_encode($promptText) }})"
                class="px-2.5 py-1 rounded-lg bg-purple-500/15 hover:bg-purple-500/25 text-purple-300 border border-purple-500/30 text-[11px] font-bold transition-all inline-flex items-center gap-1">
            <svg class="w-3 h-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            <span class="copy-label">AI Prompt</span>
        </button>

        <!-- Toggle Resolve / Reopen -->
        <form action="{{ route('admin.errors.resolve', $error->id) }}" method="POST" class="inline">
            @csrf
            @if($error->resolved)
                <button type="submit" class="px-2.5 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 text-[11px] font-semibold border border-white/10 transition-colors" title="Reopen Error">
                    Reopen
                </button>
            @else
                <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 text-[11px] font-bold transition-colors" title="Mark Resolved">
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