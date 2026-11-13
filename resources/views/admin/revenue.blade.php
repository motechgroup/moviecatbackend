@extends('layouts.app', ['title' => 'Revenue & Subscriptions Intelligence'])

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- HERO HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-6 sm:p-8 rounded-3xl glass-panel relative overflow-hidden border border-white/10 shadow-2xl">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-gradient-to-br from-amber-500/10 via-emerald-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>

        <div class="space-y-2 relative z-10">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    VIP Monetization Active
                </span>
                <span class="text-xs text-slate-400 font-mono">• Real-Time Payment Inflow</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <span>💳 Revenue & Subscriptions Intelligence</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 max-w-2xl leading-relaxed">
                Track revenue performance across Safaricom M-Pesa, PayHero, and Flutterwave. Monitor active VIP pass holders, subscription renewals, ARPU, and pass activation lifecycles.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 relative z-10">
            <a href="{{ route('admin.settings.index') }}" class="px-3.5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-bold text-slate-200 transition-all flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                <span>Gateway Config</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-white text-xs font-bold shadow-lg shadow-amber-600/30 transition-all flex items-center gap-1.5">
                <span>Dashboard</span>
                <span class="text-xs">&rarr;</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- KEY REVENUE & SUBSCRIBER KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- 1. Total Revenue Inflow -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/10 group hover:border-amber-500/40 transition-all">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Lifetime Revenue</span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-mono font-bold text-xs">
                    KES
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-amber-300 mt-2 tracking-tight font-mono">
                {{ number_format($metrics['totalRevenue']) }} <span class="text-xs font-sans text-amber-400 font-bold">{{ $metrics['currency'] }}</span>
            </div>
            <div class="space-y-1 mt-3 pt-3 border-t border-white/5 text-[11px]">
                <div class="flex items-center justify-between text-slate-300">
                    <span>This Month:</span>
                    <span class="font-bold text-emerald-400 font-mono">{{ number_format($metrics['thisMonthRevenue']) }} {{ $metrics['currency'] }}</span>
                </div>
                <div class="flex items-center justify-between text-slate-400 text-[10px]">
                    <span>Today:</span>
                    <span class="font-mono text-white">{{ number_format($metrics['todayRevenue']) }} {{ $metrics['currency'] }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Active VIP Subscribers -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/10 group hover:border-emerald-500/40 transition-all">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Active VIP Passes</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-emerald-400 mt-2 tracking-tight font-mono">
                {{ number_format($metrics['activeSubscribers']) }}
            </div>
            <div class="space-y-1 mt-3 pt-3 border-t border-white/5 text-[11px]">
                <div class="flex items-center justify-between text-emerald-400 font-bold">
                    <span class="flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Unlimited Vault Active
                    </span>
                    @if($metrics['expiringSoonCount'] > 0)
                        <span class="text-amber-400 text-[10px] font-mono">{{ $metrics['expiringSoonCount'] }} exp in <48h</span>
                    @endif
                </div>
                <div class="flex items-center justify-between text-slate-400 text-[10px]">
                    <span>Lifetime Pass Holders:</span>
                    <span class="font-mono text-white">{{ number_format($metrics['successfulCount']) }}</span>
                </div>
            </div>
        </div>

        <!-- 3. Unit Economics & ARPU -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/10 group hover:border-purple-500/40 transition-all">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Subscriber ARPU</span>
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-purple-300 mt-2 tracking-tight font-mono">
                {{ $metrics['currency'] }} {{ number_format($metrics['arpu']) }}
            </div>
            <div class="space-y-1 mt-3 pt-3 border-t border-white/5 text-[11px]">
                <div class="flex items-center justify-between text-slate-300">
                    <span>Fleet Conversion Rate:</span>
                    <span class="font-bold text-purple-400 font-mono">{{ $metrics['conversionRate'] }}% of fleet</span>
                </div>
                <div class="flex items-center justify-between text-slate-400 text-[10px]">
                    <span>7-Day Inflow:</span>
                    <span class="font-mono text-white">{{ number_format($metrics['thisWeekRevenue']) }} {{ $metrics['currency'] }}</span>
                </div>
            </div>
        </div>

        <!-- 4. Payment Gateway Success & Volume -->
        <div class="p-5 rounded-3xl glass-panel relative overflow-hidden border border-white/10 group hover:border-cyan-500/40 transition-all">
            <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                <span>Transaction Health</span>
                <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-cyan-400 mt-2 tracking-tight font-mono">
                {{ $metrics['successRate'] }}% <span class="text-xs font-normal text-slate-400 font-sans">Success</span>
            </div>
            <div class="space-y-1 mt-3 pt-3 border-t border-white/5 text-[11px]">
                <div class="flex items-center justify-between text-slate-300">
                    <span>Total Attempts:</span>
                    <span class="font-bold text-white font-mono">{{ number_format($metrics['totalSubscriptions']) }}</span>
                </div>
                <div class="flex items-center justify-between text-slate-400 text-[10px]">
                    <span>Pending: <strong class="text-amber-400 font-mono">{{ $metrics['pendingCount'] }}</strong></span>
                    <span>Expired: <strong class="text-slate-500 font-mono">{{ $metrics['expiredCount'] }}</strong></span>
                </div>
            </div>
        </div>

    </div>

    <!-- PLAN BREAKDOWN & GATEWAY PERFORMANCE MODULES (2 COLUMNS) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Column 1: Plan Tier & Period Demand -->
        <div class="p-6 sm:p-7 rounded-3xl glass-panel space-y-4 border border-white/10 shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                        <span>📦 Plan Tier & Duration Demand</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Audience preference between weekly and monthly VIP access passes.</p>
                </div>
                <span class="text-[10px] font-mono text-amber-400 font-bold bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">Pass Velocity</span>
            </div>

            <div class="space-y-3.5 pt-2">
                <!-- 1 Week Plan -->
                <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2 font-bold text-amber-300">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                            <span>1 Week VIP Pass (7 Days)</span>
                        </div>
                        <span class="font-mono text-white font-bold">{{ $metrics['plans']['week']['share'] }}% share</span>
                    </div>
                    <div class="w-full bg-white/5 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-amber-400 h-full rounded-full transition-all duration-500" style="width: {{ $metrics['plans']['week']['share'] }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] font-mono text-slate-400 pt-0.5">
                        <span>{{ number_format($metrics['plans']['week']['count']) }} active passes</span>
                        <span class="text-amber-300 font-bold">{{ number_format($metrics['plans']['week']['revenue']) }} {{ $metrics['currency'] }}</span>
                    </div>
                </div>

                <!-- 1 Month Plan -->
                <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2 font-bold text-emerald-300">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                            <span>1 Month VIP Pass (30 Days)</span>
                        </div>
                        <span class="font-mono text-white font-bold">{{ $metrics['plans']['month']['share'] }}% share</span>
                    </div>
                    <div class="w-full bg-white/5 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-500" style="width: {{ $metrics['plans']['month']['share'] }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] font-mono text-slate-400 pt-0.5">
                        <span>{{ number_format($metrics['plans']['month']['count']) }} active passes</span>
                        <span class="text-emerald-300 font-bold">{{ number_format($metrics['plans']['month']['revenue']) }} {{ $metrics['currency'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Column 2: Payment Gateway Inflow -->
        <div class="p-6 sm:p-7 rounded-3xl glass-panel space-y-4 border border-white/10 shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                        <span>🏦 Payment Gateway Distribution</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Inflow collected across Safaricom M-Pesa, PayHero, and Flutterwave.</p>
                </div>
                <span class="text-[10px] font-mono text-emerald-400 font-bold bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">Gateways</span>
            </div>

            <div class="space-y-3 pt-2">
                <!-- Safaricom M-Pesa -->
                <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 font-black text-xs border border-emerald-500/20">
                            M
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white">Safaricom M-Pesa</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $metrics['gateways']['mpesa']['count'] }} transactions ({{ $metrics['gateways']['mpesa']['share'] }}% share)</div>
                        </div>
                    </div>
                    <div class="text-right font-mono">
                        <div class="text-sm font-black text-emerald-400">{{ number_format($metrics['gateways']['mpesa']['revenue']) }}</div>
                        <div class="text-[10px] text-slate-500">{{ $metrics['currency'] }}</div>
                    </div>
                </div>

                <!-- PayHero Gateway -->
                <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-400 font-black text-xs border border-cyan-500/20">
                            P
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white">PayHero Mobile Gateway</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $metrics['gateways']['payhero']['count'] }} transactions ({{ $metrics['gateways']['payhero']['share'] }}% share)</div>
                        </div>
                    </div>
                    <div class="text-right font-mono">
                        <div class="text-sm font-black text-cyan-400">{{ number_format($metrics['gateways']['payhero']['revenue']) }}</div>
                        <div class="text-[10px] text-slate-500">{{ $metrics['currency'] }}</div>
                    </div>
                </div>

                <!-- Flutterwave -->
                <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 font-black text-xs border border-indigo-500/20">
                            F
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white">Flutterwave Global</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $metrics['gateways']['flutterwave']['count'] }} transactions ({{ $metrics['gateways']['flutterwave']['share'] }}% share)</div>
                        </div>
                    </div>
                    <div class="text-right font-mono">
                        <div class="text-sm font-black text-indigo-400">{{ number_format($metrics['gateways']['flutterwave']['revenue']) }}</div>
                        <div class="text-[10px] text-slate-500">{{ $metrics['currency'] }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- SUBSCRIPTIONS MANAGEMENT TABLE & MULTI-FILTER BAR -->
    <div class="p-6 sm:p-7 rounded-3xl glass-panel space-y-5 border border-white/10 shadow-2xl">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                    <span>📋 VIP Subscriptions & Pass Ledger</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Filter, search, inspect transactions, and manage active VIP pass entitlements.</p>
            </div>
        </div>

        <!-- SEARCH & MULTI-FILTER BAR -->
        <form method="GET" action="{{ route('admin.revenue.index') }}" class="space-y-3 pt-1">
            <!-- Status Pills Row -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.revenue.index', array_merge(request()->query(), ['status' => 'all'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ ($filters['status'] === 'all') ? 'bg-white/15 text-white border border-white/20' : 'bg-white/5 text-slate-400 hover:text-white' }}">
                    All Passes ({{ $metrics['totalSubscriptions'] }})
                </a>
                <a href="{{ route('admin.revenue.index', array_merge(request()->query(), ['status' => 'active'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ ($filters['status'] === 'active') ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-white/5 text-slate-400 hover:text-white' }}">
                    🟢 Active VIP ({{ $metrics['activeSubscribers'] }})
                </a>
                <a href="{{ route('admin.revenue.index', array_merge(request()->query(), ['status' => 'expiring_soon'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ ($filters['status'] === 'expiring_soon') ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-white/5 text-slate-400 hover:text-white' }}">
                    ⏳ Expiring Soon (<48h) ({{ $metrics['expiringSoonCount'] }})
                </a>
                <a href="{{ route('admin.revenue.index', array_merge(request()->query(), ['status' => 'pending'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ ($filters['status'] === 'pending') ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' : 'bg-white/5 text-slate-400 hover:text-white' }}">
                    ⏱️ Pending ({{ $metrics['pendingCount'] }})
                </a>
                <a href="{{ route('admin.revenue.index', array_merge(request()->query(), ['status' => 'expired'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ ($filters['status'] === 'expired') ? 'bg-slate-700/40 text-slate-300 border border-white/10' : 'bg-white/5 text-slate-400 hover:text-white' }}">
                    Expired ({{ $metrics['expiredCount'] }})
                </a>
                <a href="{{ route('admin.revenue.index', array_merge(request()->query(), ['status' => 'failed'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ ($filters['status'] === 'failed') ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-white/5 text-slate-400 hover:text-white' }}">
                    Failed ({{ $metrics['failedCount'] }})
                </a>
            </div>

            <!-- Dropdown Selectors & Search Input -->
            <div class="flex flex-wrap items-center gap-2.5 pt-1">
                <!-- Search Input -->
                <div class="relative flex-1 min-w-[220px]">
                    <input type="text" 
                           name="q" 
                           value="{{ $filters['q'] }}" 
                           placeholder="Search user, phone, reference, receipt, device..." 
                           class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3.5 py-2 pl-9 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-400" />
                    <svg class="w-3.5 h-3.5 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Gateway Selector -->
                <select name="gateway" onchange="this.form.submit()" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-amber-400">
                    <option value="all" {{ $filters['gateway'] === 'all' ? 'selected' : '' }}>All Gateways</option>
                    <option value="mpesa" {{ $filters['gateway'] === 'mpesa' ? 'selected' : '' }}>Safaricom M-Pesa</option>
                    <option value="payhero" {{ $filters['gateway'] === 'payhero' ? 'selected' : '' }}>PayHero</option>
                    <option value="flutterwave" {{ $filters['gateway'] === 'flutterwave' ? 'selected' : '' }}>Flutterwave</option>
                </select>

                <!-- Plan Selector -->
                <select name="plan" onchange="this.form.submit()" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-amber-400">
                    <option value="all" {{ $filters['plan'] === 'all' ? 'selected' : '' }}>All Plans</option>
                    <option value="week" {{ $filters['plan'] === 'week' ? 'selected' : '' }}>1 Week (7 Days)</option>
                    <option value="month" {{ $filters['plan'] === 'month' ? 'selected' : '' }}>1 Month (30 Days)</option>
                </select>

                <!-- Date Range Period Selector -->
                <select name="period" onchange="this.form.submit()" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-amber-400">
                    <option value="all" {{ ($filters['period'] ?? 'all') === 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ ($filters['period'] ?? '') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ ($filters['period'] ?? '') === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ ($filters['period'] ?? '') === 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="last30" {{ ($filters['period'] ?? '') === 'last30' ? 'selected' : '' }}>Last 30 Days</option>
                </select>

                <!-- Sort By Selector -->
                <select name="sort" onchange="this.form.submit()" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-amber-400">
                    <option value="newest" {{ ($filters['sort'] ?? 'newest') === 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="highest" {{ ($filters['sort'] ?? '') === 'highest' ? 'selected' : '' }}>Highest Amount</option>
                    <option value="lowest" {{ ($filters['sort'] ?? '') === 'lowest' ? 'selected' : '' }}>Lowest Amount</option>
                    <option value="expiring_soon" {{ ($filters['sort'] ?? '') === 'expiring_soon' ? 'selected' : '' }}>Expiring Soonest</option>
                    <option value="oldest" {{ ($filters['sort'] ?? '') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                </select>

                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold transition-all shadow-md shadow-amber-600/25">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'gateway', 'plan', 'status', 'period', 'sort']))
                    <a href="{{ route('admin.revenue.index') }}" class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 text-xs font-semibold transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-white/10 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-3">Subscriber / Account</th>
                        <th class="py-3 px-3">Plan & Tier</th>
                        <th class="py-3 px-3">Gateway & M-Pesa Code</th>
                        <th class="py-3 px-3 text-right">Amount</th>
                        <th class="py-3 px-3 text-center">Entitlement Status</th>
                        <th class="py-3 px-3">Activated / Expiry</th>
                        <th class="py-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-300">
                    @forelse($subscriptions as $sub)
                        @php
                            $isActive = $sub->isActive();
                            $receipt = $sub->metadata['receipt'] ?? $sub->metadata['verify']['provider_reference'] ?? $sub->metadata['verify']['third_party_reference'] ?? null;
                            $daysLeft = ($sub->expires_at && $sub->expires_at->isFuture()) ? now()->diffInDays($sub->expires_at) : 0;
                            $hoursLeft = ($sub->expires_at && $sub->expires_at->isFuture()) ? now()->diffInHours($sub->expires_at) : 0;
                            $isExpiringSoon = $isActive && $daysLeft < 2;
                        @endphp
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <!-- Subscriber / Account -->
                            <td class="py-3 px-3">
                                <div class="font-bold text-white flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full {{ $isActive ? 'bg-emerald-400 animate-pulse' : 'bg-slate-600' }}"></span>
                                    <span>{{ $sub->username }}</span>
                                </div>
                                @if($sub->phone)
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $sub->phone }}</div>
                                @endif
                                @if($sub->device_id)
                                    <a href="{{ route('admin.devices.show', $sub->device_id) }}" class="text-[10px] text-slate-500 hover:text-amber-400 font-mono truncate max-w-[150px] block transition-colors">
                                        {{ $sub->device->device_name ?? 'Device' }}: {{ $sub->device_id }}
                                    </a>
                                @endif
                            </td>

                            <!-- Plan & Tier -->
                            <td class="py-3 px-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ str_contains($sub->package_id, 'week') ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30' : 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' }}">
                                    {{ str_contains($sub->package_id, 'week') ? '1 Week (7D)' : '1 Month (30D)' }}
                                </span>
                                <div class="text-[10px] text-slate-400 mt-1 font-mono">
                                    {{ $sub->period_days ?: (str_contains($sub->package_id, 'week') ? 7 : 30) }} Days VIP Pass
                                </div>
                            </td>

                            <!-- Gateway & Reference -->
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-bold uppercase tracking-wider {{ $sub->gateway === 'mpesa' ? 'bg-emerald-500/20 text-emerald-300' : ($sub->gateway === 'payhero' ? 'bg-cyan-500/20 text-cyan-300' : 'bg-indigo-500/20 text-indigo-300') }}">
                                        {{ $sub->gateway }}
                                    </span>
                                    @if($receipt)
                                        <span class="font-mono text-[10px] font-bold text-amber-300 bg-amber-500/15 px-1.5 py-0.5 rounded border border-amber-500/30">{{ $receipt }}</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-500 font-mono mt-0.5 truncate max-w-[160px]" title="{{ $sub->reference }}">
                                    Ref: {{ $sub->reference }}
                                </div>
                            </td>

                            <!-- Amount -->
                            <td class="py-3 px-3 text-right">
                                <div class="font-black text-white font-mono text-sm">
                                    {{ number_format($sub->amount) }} <span class="text-[10px] text-slate-400 font-sans">{{ $sub->currency }}</span>
                                </div>
                            </td>

                            <!-- Entitlement Status -->
                            <td class="py-3 px-3 text-center">
                                @if($isActive)
                                    @if($isExpiringSoon)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                            <span>⏳</span> Expiring Soon
                                        </span>
                                        <div class="text-[9px] text-amber-400 font-bold font-mono mt-0.5">{{ $hoursLeft }}h left</div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            ACTIVE VIP
                                        </span>
                                        <div class="text-[9px] text-emerald-400 font-bold font-mono mt-0.5">{{ $daysLeft }}d left</div>
                                    @endif
                                @elseif($sub->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        Pending PIN
                                    </span>
                                @elseif($sub->status === 'failed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                        Failed
                                    </span>
                                @elseif($sub->status === 'revoked')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600/30 text-rose-300 border border-rose-500/50">
                                        Revoked
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-700/40 text-slate-400 border border-white/5">
                                        Expired
                                    </span>
                                @endif
                            </td>

                            <!-- Activated / Expiry -->
                            <td class="py-3 px-3">
                                @if($sub->starts_at)
                                    <div class="text-[11px] text-slate-200 font-mono">{{ $sub->starts_at->format('d M Y, H:i') }}</div>
                                @else
                                    <div class="text-[11px] text-slate-500 font-mono">Created {{ $sub->created_at->format('d M Y') }}</div>
                                @endif

                                @if($sub->expires_at)
                                    <div class="text-[10px] text-slate-400 mt-0.5 font-mono">
                                        Exp: <span class="{{ $isActive ? ($isExpiringSoon ? 'text-amber-400 font-bold' : 'text-emerald-300 font-bold') : 'text-slate-500' }}">{{ $sub->expires_at->format('d M Y') }}</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if(!$isActive)
                                        <form action="{{ route('admin.revenue.activate', $sub->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Manually activate VIP pass for {{ $sub->username }}?')" class="px-2.5 py-1 rounded-lg bg-emerald-500/15 text-emerald-300 hover:bg-emerald-500/25 border border-emerald-500/30 text-[10px] font-bold transition-all">
                                                Activate
                                            </button>
                                        </form>
                                    @else
                                        <!-- Revoke -->
                                        <form action="{{ route('admin.revenue.cancel', $sub->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Revoke VIP access for {{ $sub->username }}?')" title="Revoke VIP Access" class="px-2.5 py-1 rounded-lg bg-rose-500/15 text-rose-300 hover:bg-rose-500/25 border border-rose-500/30 text-[10px] font-bold transition-all">
                                                Revoke
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-xs text-slate-400">
                                No subscription records match your active filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($subscriptions->hasPages())
            <div class="pt-4 border-t border-white/5">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
