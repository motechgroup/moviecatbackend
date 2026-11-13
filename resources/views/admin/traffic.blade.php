@extends('layouts.app', ['title' => 'Global Users Region & Traffic Telemetry'])

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- HERO HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-6 sm:p-8 rounded-3xl glass-panel relative overflow-hidden border border-white/10 shadow-2xl">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-gradient-to-br from-teal-500/10 via-cyan-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>

        <div class="space-y-2 relative z-10">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-teal-500/15 text-teal-300 border border-teal-500/30">
                    <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                    Live Geo Routing Active
                </span>
                <span class="text-xs text-slate-400 font-mono">• Edge Telemetry & Distribution</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <span>🌍 Users Region & Traffic Module</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 max-w-2xl leading-relaxed">
                Geographic audience density, active regional footprints, city-level streaming velocity, estimated CDN egress traffic, and real-time edge connection telemetry.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 relative z-10">
            <a href="{{ route('admin.devices.index') }}" class="px-3.5 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 text-xs font-bold transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Fleet Devices</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-500 hover:to-cyan-500 text-white text-xs font-bold shadow-lg shadow-teal-600/30 transition-all flex items-center gap-1.5">
                <span>Dashboard Overview</span>
                <span class="text-xs">&rarr;</span>
            </a>
        </div>
    </div>

    <!-- PRIMARY 4-CARD REGIONAL KPI ROW -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- 1. Reachable Countries -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/10 group hover:border-teal-500/40 transition-all">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Global Reach</span>
                <div class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-white mt-2 tracking-tight">
                {{ $stats['totalCountries'] }} <span class="text-xs text-teal-400 font-mono font-normal">Countries</span>
            </div>
            <div class="space-y-1 mt-3 pt-3 border-t border-white/5 text-[11px]">
                <div class="flex items-center justify-between text-slate-300">
                    <span>Kenya (Domestic):</span>
                    <span class="font-bold text-teal-400 font-mono">{{ $stats['domesticPct'] }}% ({{ $stats['domesticDevices'] }})</span>
                </div>
                <div class="flex items-center justify-between text-slate-400 text-[10px]">
                    <span>International:</span>
                    <span class="font-mono text-cyan-300">{{ $stats['internationalPct'] }}% ({{ $stats['internationalDevices'] }})</span>
                </div>
            </div>
        </div>

        <!-- 2. Active Metropolitan Cities -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/10 group hover:border-cyan-500/40 transition-all">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Active Cities</span>
                <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-white mt-2 tracking-tight">
                {{ $stats['totalCities'] }} <span class="text-xs text-cyan-400 font-mono font-normal">Cities</span>
            </div>
            <div class="space-y-1 mt-3 pt-3 border-t border-white/5 text-[11px]">
                <div class="flex items-center justify-between text-slate-300">
                    <span>Active Fleet:</span>
                    <span class="font-bold text-white font-mono">{{ number_format($stats['totalDevices']) }} devices</span>
                </div>
                <div class="flex items-center justify-between text-slate-400 text-[10px]">
                    <span>Total Audience Sessions:</span>
                    <span class="font-mono text-slate-300">{{ number_format($stats['totalSessions']) }}</span>
                </div>
            </div>
        </div>

        <!-- 3. Network Egress & Bandwidth -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/10 group hover:border-emerald-500/40 transition-all">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Estimated Bandwidth Egress</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-emerald-400 mt-2 tracking-tight">
                {{ $stats['totalBandwidth'] }}
            </div>
            <div class="space-y-1 mt-3 pt-3 border-t border-white/5 text-[11px]">
                <div class="flex items-center justify-between text-slate-300">
                    <span>Edge Acceleration:</span>
                    <span class="font-bold text-emerald-400 font-mono">Cloudflare CDN</span>
                </div>
                <div class="flex items-center justify-between text-slate-400 text-[10px]">
                    <span>Total Stream Time:</span>
                    <span class="font-mono text-slate-300">{{ $stats['totalWatchTime'] }}</span>
                </div>
            </div>
        </div>

        <!-- 4. Top Country Velocity -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/10 group hover:border-purple-500/40 transition-all">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Top Regional Hub</span>
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            @php
                $topCountry = $countries->first();
            @endphp
            <div class="text-2xl sm:text-3xl font-black text-purple-300 mt-2 tracking-tight truncate flex items-center gap-2">
                <span>{{ $topCountry->flag ?? '🌐' }}</span>
                <span class="truncate">{{ $topCountry->country ?? 'Global' }}</span>
            </div>
            <div class="space-y-1 mt-3 pt-3 border-t border-white/5 text-[11px]">
                <div class="flex items-center justify-between text-slate-300">
                    <span>Audience Share:</span>
                    <span class="font-bold text-purple-400 font-mono">{{ $topCountry->share_pct ?? 0 }}% of global</span>
                </div>
                <div class="flex items-center justify-between text-slate-400 text-[10px]">
                    <span>Regional Watch Time:</span>
                    <span class="font-mono text-slate-300">{{ $topCountry->formatted_watch_time ?? '0 min' }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- GEOGRAPHIC REACH & COUNTRY PERFORMANCE LEADERBOARD -->
    <div class="p-6 sm:p-7 rounded-3xl glass-panel space-y-5 border border-white/10 shadow-2xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                    <span>🌐 Country Traffic Leaderboard</span>
                    <span class="text-[10px] px-2 py-0.5 rounded font-extrabold bg-teal-500/20 text-teal-300 border border-teal-500/30">{{ $countries->count() }} Active Regions</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Audience fleet, active devices, total watch velocity, and platform ecosystem per territory.</p>
            </div>
            <span class="text-xs text-slate-400 font-mono">Sorted by Active Fleet Size</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($countries as $c)
                <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 hover:border-teal-500/30 hover:bg-white/[0.04] transition-all space-y-3 group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="text-3xl group-hover:scale-110 transition-transform">{{ $c->flag }}</span>
                            <div>
                                <div class="text-sm font-bold text-white group-hover:text-teal-300 transition-colors">{{ $c->country }}</div>
                                <span class="text-[10px] font-mono text-slate-400 uppercase">{{ $c->country_code }}</span>
                            </div>
                        </div>
                        <span class="px-2 py-1 rounded-lg text-xs font-bold font-mono bg-teal-500/15 text-teal-300 border border-teal-500/30">
                            {{ $c->share_pct }}%
                        </span>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400">Registered Devices:</span>
                            <span class="font-bold text-white font-mono">{{ number_format($c->device_count) }}</span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-teal-500 to-cyan-400 h-full rounded-full" style="width: {{ $c->share_pct }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-white/5 text-[10px] font-mono text-slate-400">
                        <div>
                            <span class="text-slate-500 block">Watch Time:</span>
                            <span class="text-purple-300 font-bold">{{ $c->formatted_watch_time }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Active Today:</span>
                            <span class="text-emerald-400 font-bold">{{ $c->active_today }} devices</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 text-[10px] font-mono text-slate-500">
                        <span>Android: {{ $c->android_count }}</span>
                        <span>iOS: {{ $c->ios_count }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-xs text-slate-400">
                    No country traffic telemetry captured yet. Incoming connections will automatically populate here.
                </div>
            @endforelse
        </div>
    </div>

    <!-- 2-COLUMN ANALYTICS: TOP CITIES & REGIONAL PLATFORM SHARE -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Column 1: Top Streaming Cities -->
        <div class="p-6 sm:p-7 rounded-3xl glass-panel space-y-4 border border-white/10 shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                        <span>🏙️ Top Streaming Cities</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Metropolitan regions with highest audience engagement.</p>
                </div>
                <span class="text-[10px] font-mono text-cyan-400 font-bold bg-cyan-500/10 px-2 py-0.5 rounded border border-cyan-500/20">Urban Density</span>
            </div>

            <div class="space-y-2.5">
                @forelse($topCities as $idx => $city)
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-white/[0.02] border border-white/5 hover:bg-white/[0.04] transition-all">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-lg font-bold text-xs flex items-center justify-center {{ $idx === 0 ? 'bg-amber-400/20 text-amber-300' : 'bg-white/5 text-slate-400' }}">
                                #{{ $idx + 1 }}
                            </span>
                            <span class="text-xl">{{ $city->flag }}</span>
                            <div>
                                <div class="text-xs font-bold text-white">{{ $city->city }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $city->country }} ({{ $city->country_code }})</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-bold text-cyan-300 font-mono">{{ number_format($city->device_count) }} devices</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $city->formatted_watch_time }}</div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">
                        No city geolocation data captured yet. Ensure mobile devices have location/IP headers enabled.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Column 2: Domestic vs International Audience Distribution -->
        <div class="p-6 sm:p-7 rounded-3xl glass-panel space-y-4 border border-white/10 shadow-2xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                            <span>📊 Geographic Distribution Ratio</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Domestic core market vs. international audience footprint.</p>
                    </div>
                    <span class="text-[10px] font-mono text-teal-400 font-bold bg-teal-500/10 px-2 py-0.5 rounded border border-teal-500/20">Market Spread</span>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-teal-300 flex items-center gap-2">
                                <span>🇰🇪 Domestic Audience (Kenya)</span>
                            </span>
                            <span class="font-mono text-white font-bold">{{ $stats['domesticPct'] }}% ({{ number_format($stats['domesticDevices']) }} devices)</span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-teal-500 to-emerald-400 h-full rounded-full transition-all duration-500" style="width: {{ $stats['domesticPct'] }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-cyan-300 flex items-center gap-2">
                                <span>🌐 International Diaspora & Global</span>
                            </span>
                            <span class="font-mono text-white font-bold">{{ $stats['internationalPct'] }}% ({{ number_format($stats['internationalDevices']) }} devices)</span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-cyan-500 to-sky-400 h-full rounded-full transition-all duration-500" style="width: {{ $stats['internationalPct'] }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-6 pt-6 border-t border-white/5 text-xs">
                    <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/5">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block">Total Fleet Reach</span>
                        <span class="text-lg font-black text-white mt-0.5 block font-mono">{{ number_format($stats['totalDevices']) }}</span>
                        <span class="text-[10px] text-slate-500">Global active units</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/5">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block">Avg Sessions / Unit</span>
                        @php
                            $avgSessions = $stats['totalDevices'] > 0 ? round($stats['totalSessions'] / $stats['totalDevices'], 1) : 0;
                        @endphp
                        <span class="text-lg font-black text-teal-400 mt-0.5 block font-mono">{{ $avgSessions }}</span>
                        <span class="text-[10px] text-slate-500">Lifetime frequency</span>
                    </div>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-teal-500/5 border border-teal-500/20 flex items-center justify-between text-xs mt-4">
                <span class="text-slate-300">Edge Gateway: Cloudflare Anycast CDN Routing</span>
                <span class="text-teal-400 font-bold font-mono">100% Operational</span>
            </div>
        </div>

    </div>

    <!-- LIVE REGIONAL TRAFFIC & CONNECTION FEED TABLE -->
    <div class="p-6 sm:p-7 rounded-3xl glass-panel space-y-5 border border-white/10 shadow-2xl">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                    <span>⚡ Real-Time Regional Connections Telemetry</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Live device heartbeat pings, IP routing, client OS, and regional session activity.</p>
            </div>

            <!-- Multi-Filter Bar -->
            <form method="GET" action="{{ route('admin.traffic.index') }}" class="flex flex-wrap items-center gap-2.5">
                <!-- Search input -->
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search city, IP, device..." 
                           class="w-48 sm:w-56 px-3.5 py-2 pl-9 rounded-xl bg-white/5 border border-white/10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500" />
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Country selector -->
                <select name="country" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-xs text-slate-300 focus:outline-none focus:border-teal-500">
                    <option value="">All Countries</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->country_code }}" {{ request('country') === $c->country_code ? 'selected' : '' }}>
                            {{ $c->flag }} {{ $c->country }}
                        </option>
                    @endforeach
                </select>

                <!-- Platform selector -->
                <select name="platform" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-xs text-slate-300 focus:outline-none focus:border-teal-500">
                    <option value="">All Platforms</option>
                    <option value="android" {{ request('platform') === 'android' ? 'selected' : '' }}>Android</option>
                    <option value="ios" {{ request('platform') === 'ios' ? 'selected' : '' }}>iOS</option>
                </select>

                <button type="submit" class="px-3 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold transition-all">Filter</button>
                @if(request()->hasAny(['search', 'country', 'platform']))
                    <a href="{{ route('admin.traffic.index') }}" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 text-xs transition-all">Reset</a>
                @endif
            </form>
        </div>

        <!-- Connection Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3 px-3">Region & City</th>
                        <th class="py-3 px-3">Device & User</th>
                        <th class="py-3 px-3">IP Address</th>
                        <th class="py-3 px-3">Platform & App</th>
                        <th class="py-3 px-3">Watch Duration</th>
                        <th class="py-3 px-3">Sessions</th>
                        <th class="py-3 px-3 text-right">Last Active</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-xs">
                    @forelse($trafficDevices as $dev)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">{{ $dev->country_flag ?: '🌐' }}</span>
                                    <div>
                                        <div class="font-bold text-white">{{ $dev->country ?: 'Global' }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $dev->city ?: 'Unknown City' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <a href="{{ route('admin.devices.show', $dev->device_id) }}" class="font-bold text-white hover:text-teal-400 transition-colors block">
                                    {{ $dev->display_name }}
                                </a>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $dev->device_name ?: 'Mobile Device' }}</span>
                            </td>
                            <td class="py-3 px-3">
                                @php
                                    $ip = $dev->ip_address ?: '127.0.0.1';
                                    $parts = explode('.', $ip);
                                    $maskedIp = count($parts) === 4 ? "{$parts[0]}.{$parts[1]}.xxx.{$parts[3]}" : $ip;
                                @endphp
                                <span class="font-mono text-slate-300 text-[11px] bg-white/5 px-2 py-0.5 rounded border border-white/5">
                                    {{ $maskedIp }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $dev->platform === 'ios' ? 'bg-sky-500/15 text-sky-400' : 'bg-emerald-500/15 text-emerald-400' }}">
                                        {{ $dev->platform }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono">v{{ $dev->app_version ?: '1.0' }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 font-mono text-purple-300 font-bold">
                                {{ $dev->formatted_watch_time }}
                            </td>
                            <td class="py-3 px-3 font-mono text-slate-300">
                                {{ $dev->sessions_count }}
                            </td>
                            <td class="py-3 px-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($dev->isOnline())
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <span class="text-emerald-400 font-bold text-[11px]">Online</span>
                                    @else
                                        <span class="text-slate-400 text-[11px] font-mono">{{ $dev->last_active_at ? $dev->last_active_at->diffForHumans() : 'Never' }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-xs text-slate-400">
                                No regional device connections match your active filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($trafficDevices->hasPages())
            <div class="pt-4 border-t border-white/5">
                {{ $trafficDevices->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
