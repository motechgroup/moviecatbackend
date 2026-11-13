@extends('layouts.app', ['title' => 'Active Devices Fleet & Retention Intelligence'])

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- HERO HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-6 sm:p-8 rounded-3xl glass-panel relative overflow-hidden border border-white/10 shadow-2xl">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-gradient-to-br from-emerald-500/10 via-teal-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>

        <div class="space-y-2 relative z-10">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ $stats['activeNow'] }} Online Now
                </span>
                <span class="text-xs text-slate-400 font-mono">• Fleet Retention & Device Telemetry</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <span>📱 Active Devices Fleet & Retention Intelligence</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 max-w-2xl leading-relaxed">
                Registered mobile clients, returning user loyalty, DAU/MAU stickiness, audience retention lifecycle, and real-time device telemetry.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 relative z-10">
            <a href="{{ route('admin.traffic.index') }}" class="px-3.5 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-teal-300 border border-teal-500/30 text-xs font-bold transition-all flex items-center gap-1.5">
                <span>🌍</span>
                <span>Regional Traffic</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-1.5">
                <span>Dashboard</span>
                <span class="text-xs">&rarr;</span>
            </a>
        </div>
    </div>

    <!-- PRIMARY 5-CARD RETENTION & FLEET BENTO ROW -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

        <!-- 1. Registered Fleet & DAU -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/10 group hover:border-emerald-500/40 transition-all">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Total Fleet</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-white mt-2 tracking-tight">
                {{ number_format($stats['total']) }}
            </div>
            <div class="space-y-1 mt-3 pt-3 border-t border-white/5 text-[11px]">
                <div class="flex items-center justify-between text-emerald-400 font-bold">
                    <span class="flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        {{ $stats['activeNow'] }} online now
                    </span>
                    <span class="text-slate-400 font-mono">{{ number_format($stats['dau']) }} DAU</span>
                </div>
            </div>
        </div>

        <!-- 2. Returning Devices & Loyalty Rate -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/10 group hover:border-teal-500/40 transition-all">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Returning Devices</span>
                <div class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-teal-400 mt-2 tracking-tight">
                {{ $stats['returningRate'] }}%
            </div>
            <div class="space-y-1 mt-3 pt-3 border-t border-white/5 text-[11px]">
                <div class="flex items-center justify-between text-slate-300">
                    <span class="font-bold text-white font-mono">{{ number_format($stats['returningCount']) }} multi-session</span>
                    <span class="text-slate-500 font-mono">{{ number_format($stats['newCount']) }} new</span>
                </div>
            </div>
        </div>

        <!-- 3. DAU / MAU Stickiness Ratio -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/10 group hover:border-cyan-500/40 transition-all">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Stickiness (DAU/MAU)</span>
                <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-cyan-400 mt-2 tracking-tight">
                {{ $stats['stickiness'] }}%
            </div>
            <div class="space-y-1 mt-3 pt-3 border-t border-white/5 text-[11px]">
                <div class="flex items-center justify-between text-slate-300">
                    <span>WAU: <strong class="text-white font-mono">{{ number_format($stats['wau']) }}</strong></span>
                    <span>MAU: <strong class="text-white font-mono">{{ number_format($stats['mau']) }}</strong></span>
                </div>
            </div>
        </div>

        <!-- 4. Average User Depth -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/10 group hover:border-purple-500/40 transition-all">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Average User Depth</span>
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-purple-300 mt-2 tracking-tight">
                {{ $stats['avgSessions'] }} <span class="text-xs font-normal text-slate-400 font-mono">launches</span>
            </div>
            <div class="space-y-1 mt-3 pt-3 border-t border-white/5 text-[11px]">
                <div class="flex items-center justify-between text-slate-300">
                    <span>Avg Watch Time:</span>
                    <span class="font-bold text-purple-400 font-mono">{{ $stats['avgWatchFormatted'] }}</span>
                </div>
            </div>
        </div>

        <!-- 5. Platform Retention Split -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/10 group hover:border-sky-500/40 transition-all col-span-1 sm:col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Platform Retention</span>
                <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-xs font-bold text-emerald-400">Android {{ $stats['androidRetentionRate'] }}%</span>
                <span class="text-slate-600">/</span>
                <span class="text-xs font-bold text-sky-400">iOS {{ $stats['iosRetentionRate'] }}%</span>
            </div>
            <div class="w-full bg-slate-800 rounded-full h-1.5 mt-3 overflow-hidden flex">
                <div class="bg-emerald-400 h-1.5" style="width: {{ $stats['androidRetentionRate'] }}%"></div>
                <div class="bg-sky-400 h-1.5" style="width: {{ $stats['iosRetentionRate'] }}%"></div>
            </div>
            <div class="flex items-center justify-between text-[10px] text-slate-500 font-mono mt-1">
                <span>Android: {{ $stats['android'] }}</span>
                <span>iOS: {{ $stats['ios'] }}</span>
            </div>
        </div>

    </div>

    <!-- AUDIENCE LIFECYCLE & RETENTION COHORTS FUNNEL -->
    <div class="p-6 sm:p-7 rounded-3xl glass-panel space-y-4 border border-white/10 shadow-2xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                    <span>👑 Fleet Lifecycle & Retention Cohorts</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Categorization of fleet devices by usage frequency, return rates, and high-value power loyalty.</p>
            </div>
            <span class="text-[10px] font-mono text-teal-400 font-bold bg-teal-500/10 px-2.5 py-1 rounded-xl border border-teal-500/20">Audience Cohorts</span>
        </div>

        <div class="space-y-2">
            <div class="w-full bg-white/5 rounded-2xl h-3.5 overflow-hidden flex shadow-inner">
                <div class="bg-sky-500 h-full transition-all duration-500" style="width: {{ $stats['funnel']['new_pct'] }}%" title="New (1 launch): {{ $stats['funnel']['new_pct'] }}%"></div>
                <div class="bg-teal-500 h-full transition-all duration-500" style="width: {{ $stats['funnel']['returning_pct'] }}%" title="Returning (2-4 launches): {{ $stats['funnel']['returning_pct'] }}%"></div>
                <div class="bg-amber-400 h-full transition-all duration-500" style="width: {{ $stats['funnel']['power_pct'] }}%" title="Power Users (5+ launches): {{ $stats['funnel']['power_pct'] }}%"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                <!-- Cohort 1: New Users -->
                <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/5">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-sky-400 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                            First-Time / New Units
                        </span>
                        <span class="text-xs font-mono text-sky-300 font-bold">{{ $stats['funnel']['new_pct'] }}%</span>
                    </div>
                    <div class="text-lg font-black text-white mt-1">{{ number_format($stats['newCount']) }}</div>
                    <span class="text-[10px] text-slate-500">1 recorded app launch</span>
                </div>

                <!-- Cohort 2: Returning Users -->
                <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/5">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-teal-400 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                            Returning Audience
                        </span>
                        <span class="text-xs font-mono text-teal-300 font-bold">{{ $stats['funnel']['returning_pct'] }}%</span>
                    </div>
                    <div class="text-lg font-black text-teal-300 mt-1">{{ number_format(max(0, $stats['returningCount'] - $stats['powerCount'])) }}</div>
                    <span class="text-[10px] text-slate-500">2 to 4 repeated app launches</span>
                </div>

                <!-- Cohort 3: Power Loyal Users -->
                <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/5">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-amber-300 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            Power Users / High Loyalty
                        </span>
                        <span class="text-xs font-mono text-amber-300 font-bold">{{ $stats['funnel']['power_pct'] }}%</span>
                    </div>
                    <div class="text-lg font-black text-amber-300 mt-1">{{ number_format($stats['powerCount']) }}</div>
                    <span class="text-[10px] text-slate-500">5+ launches or >2h streaming</span>
                </div>
            </div>
        </div>
    </div>

    <!-- SEARCH & MULTI-FILTER BAR -->
    <div class="glass-panel p-4 sm:p-5 rounded-3xl border border-white/10 shadow-xl">
        <form method="GET" action="{{ route('admin.devices.index') }}" class="flex flex-wrap items-center gap-2.5">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search username, device ID, city, IP..." 
                       class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2 pl-9 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-emerald-500" />
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Country Filter -->
            <select name="country" onchange="this.form.submit()" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-emerald-500">
                <option value="">All Countries ({{ $availableCountries->count() }})</option>
                @foreach($availableCountries as $ac)
                    @php
                        $cCode = strtoupper($ac->country_code ?: '');
                        $cFlag = (strlen($cCode) === 2 && ctype_alpha($cCode))
                            ? mb_chr(127397 + ord($cCode[0]), 'UTF-8') . mb_chr(127397 + ord($cCode[1]), 'UTF-8')
                            : '🌐';
                    @endphp
                    <option value="{{ $ac->country_code }}" {{ request('country') === $ac->country_code ? 'selected' : '' }}>
                        {{ $cFlag }} {{ $ac->country ?: $ac->country_code }} ({{ $ac->count }})
                    </option>
                @endforeach
            </select>

            <!-- Retention / Loyalty Filter -->
            <select name="retention" onchange="this.form.submit()" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-emerald-500">
                <option value="">All Retention Cohorts</option>
                <option value="returning" {{ request('retention') === 'returning' ? 'selected' : '' }}>🔁 Returning Only ({{ $stats['returningCount'] }})</option>
                <option value="power" {{ request('retention') === 'power' ? 'selected' : '' }}>👑 Power Users ({{ $stats['powerCount'] }})</option>
                <option value="new" {{ request('retention') === 'new' ? 'selected' : '' }}>✨ First-Time Only ({{ $stats['newCount'] }})</option>
                <option value="dormant" {{ request('retention') === 'dormant' ? 'selected' : '' }}>💤 Dormant (>14d) ({{ $stats['dormantCount'] }})</option>
            </select>

            <!-- Platform Filter -->
            <select name="platform" onchange="this.form.submit()" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-emerald-500">
                <option value="">All Platforms</option>
                <option value="android" {{ request('platform') === 'android' ? 'selected' : '' }}>Android ({{ $stats['android'] }})</option>
                <option value="ios" {{ request('platform') === 'ios' ? 'selected' : '' }}>iOS ({{ $stats['ios'] }})</option>
            </select>

            <!-- Activity Status Filter -->
            <select name="status" onchange="this.form.submit()" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-emerald-500">
                <option value="">All Activity</option>
                <option value="online" {{ request('status') === 'online' ? 'selected' : '' }}>🟢 Online Now ({{ $stats['activeNow'] }})</option>
                <option value="today" {{ request('status') === 'today' ? 'selected' : '' }}>Active Today ({{ $stats['dau'] }})</option>
                <option value="week" {{ request('status') === 'week' ? 'selected' : '' }}>Active This Week ({{ $stats['wau'] }})</option>
            </select>

            <!-- Sort By -->
            <select name="sort" onchange="this.form.submit()" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-emerald-500">
                <option value="recent" {{ request('sort') === 'recent' ? 'selected' : '' }}>Recent Active</option>
                <option value="sessions" {{ request('sort') === 'sessions' ? 'selected' : '' }}>Most Sessions</option>
                <option value="watch_time" {{ request('sort') === 'watch_time' ? 'selected' : '' }}>Highest Watch Time</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>First Registered</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/25">
                Apply
            </button>
            @if(request()->hasAny(['search', 'country', 'platform', 'retention', 'status', 'sort']))
                <a href="{{ route('admin.devices.index') }}" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 text-xs font-semibold transition-all">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- DEVICES & RETENTION TELEMETRY TABLE -->
    <div class="glass-panel p-6 sm:p-7 rounded-3xl space-y-4 border border-white/10 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-white/10 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3.5 font-semibold text-center w-12 text-slate-500">#</th>
                        <th class="pb-3.5 font-semibold">User & Device ID</th>
                        <th class="pb-3.5 font-semibold">Country & IP</th>
                        <th class="pb-3.5 font-semibold">Platform & OS</th>
                        <th class="pb-3.5 font-semibold">Retention & Frequency</th>
                        <th class="pb-3.5 font-semibold">Watch History</th>
                        <th class="pb-3.5 font-semibold">Last Seen</th>
                        <th class="pb-3.5 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($devices as $device)
                        @php
                            $isOnline = $device->isOnline();
                            $isPower = $device->sessions_count >= 5 || (int)$device->total_watch_seconds >= 7200;
                            $isReturning = $device->sessions_count > 1;
                        @endphp
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-4 text-center font-mono text-slate-500 font-bold text-xs">
                                {{ ($devices->currentPage() - 1) * $devices->perPage() + $loop->iteration }}
                            </td>
                            <td class="py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-700 flex items-center justify-center text-white font-extrabold text-xs shadow border border-white/5 flex-shrink-0">
                                        {{ strtoupper(substr($device->display_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.devices.show', $device->device_id) }}" class="font-extrabold text-white text-sm hover:text-emerald-400 transition-colors">
                                                {{ $device->display_name }}
                                            </a>
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">Auto Guest</span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                            {{ $device->device_name ?: 'Mobile' }} • <span class="text-slate-500">{{ $device->device_id }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">{{ $device->country_flag }}</span>
                                    <div>
                                        <div class="font-bold text-white">{{ $device->country ?: ($device->country_code ?: 'Unknown') }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono">
                                            @php
                                                $ip = $device->ip_address ?: '127.0.0.1';
                                                $parts = explode('.', $ip);
                                                $maskedIp = count($parts) === 4 ? "{$parts[0]}.{$parts[1]}.xxx.{$parts[3]}" : $ip;
                                            @endphp
                                            {{ $maskedIp }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $device->platform === 'ios' ? 'bg-sky-500/20 text-sky-300' : 'bg-emerald-500/20 text-emerald-300' }}">
                                        {{ $device->platform }}
                                    </span>
                                    <span class="text-slate-400 text-[10px] font-mono">{{ $device->os_version ?: '1.0' }}</span>
                                </div>
                            </td>

                            <td class="py-4">
                                <div class="space-y-1">
                                    @if($isPower)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                                            <span>👑</span> Power User
                                        </span>
                                    @elseif($isReturning)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                                            <span>🔁</span> Returning
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                            <span>✨</span> New User
                                        </span>
                                    @endif
                                    <div class="text-[10px] text-slate-400 font-mono">
                                        {{ $device->sessions_count }} launches
                                    </div>
                                </div>
                            </td>

                            <td class="py-4">
                                <div class="space-y-0.5">
                                    <div class="font-bold text-white font-mono">{{ $device->formatted_watch_time }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $device->watch_progress_count ?? 0 }} titles</div>
                                </div>
                            </td>

                            <td class="py-4">
                                @if($isOnline)
                                    <span class="text-emerald-400 font-bold text-[11px] flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Online Now
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px] font-mono">{{ $device->last_active_at ? $device->last_active_at->diffForHumans() : 'Never' }}</span>
                                @endif
                            </td>

                            <td class="py-4 text-right">
                                <a href="{{ route('admin.devices.show', $device->device_id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-200 hover:text-white font-bold text-xs border border-white/10 transition-all shadow-sm">
                                    <span>Inspect</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                No mobile devices match this filter criteria.
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
