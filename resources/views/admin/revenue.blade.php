@extends('layouts.app', ['title' => 'Revenue & Subscriptions'])

@section('content')
<div class="space-y-6">

    <!-- HERO HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-3xl glass-panel relative overflow-hidden">
        <div class="space-y-1 relative z-10">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    <svg class="w-3 h-3 mr-1 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    VIP Subscriptions & Monetization
                </span>
                <span class="text-xs text-slate-400">• Real-Time Inflow</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <span>Revenue & Subscriptions</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 max-w-2xl">
                Track revenue performance across M-Pesa, PayHero, and Flutterwave. Monitor active VIP passes, subscription renewals, and offline vault pass activations.
            </p>
        </div>

        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('admin.settings.index') }}" class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-semibold text-slate-200 transition-all flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                Gateway Config
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

    <!-- KEY REVENUE KPI CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Revenue -->
        <div class="p-5 rounded-2xl glass-panel relative overflow-hidden group hover:border-amber-500/40 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-400">Total Revenue</span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400 border border-amber-500/20">
                    <span class="text-xs font-bold font-mono">KES</span>
                </div>
            </div>
            <div class="text-2xl font-black text-white font-mono">
                {{ number_format($metrics['totalRevenue']) }} <span class="text-xs font-sans text-amber-400 font-bold">{{ $metrics['currency'] }}</span>
            </div>
            <div class="text-[11px] text-slate-400 mt-2 flex items-center gap-1">
                <span class="text-emerald-400 font-bold">Lifetime Paid Inflow</span>
            </div>
        </div>

        <!-- This Month Revenue -->
        <div class="p-5 rounded-2xl glass-panel relative overflow-hidden group hover:border-emerald-500/40 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-400">This Month</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 border border-emerald-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-white font-mono">
                {{ number_format($metrics['thisMonthRevenue']) }} <span class="text-xs font-sans text-emerald-400 font-bold">{{ $metrics['currency'] }}</span>
            </div>
            <div class="text-[11px] text-slate-400 mt-2 flex items-center gap-1">
                <span>Today: <strong class="text-white">{{ number_format($metrics['todayRevenue']) }} {{ $metrics['currency'] }}</strong></span>
            </div>
        </div>

        <!-- Active VIP Subscribers -->
        <div class="p-5 rounded-2xl glass-panel relative overflow-hidden group hover:border-amber-500/40 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-400">Active Subscribers</span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400 border border-amber-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-white font-mono">
                {{ number_format($metrics['activeSubscribers']) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-2 flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-emerald-400 font-bold">Unrestricted Offline Access</span>
            </div>
        </div>

        <!-- Subscriptions Volume -->
        <div class="p-5 rounded-2xl glass-panel relative overflow-hidden group hover:border-indigo-500/40 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-400">Total Transactions</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 border border-indigo-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-white font-mono">
                {{ number_format($metrics['totalSubscriptions']) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-2 flex items-center justify-between">
                <span>Pending: <strong class="text-amber-400">{{ $metrics['pendingCount'] }}</strong></span>
                <span>Expired: <strong class="text-slate-500">{{ $metrics['expiredCount'] }}</strong></span>
            </div>
        </div>
    </div>

    <!-- GATEWAY REVENUE BREAKDOWN PILLS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 font-bold text-xs border border-emerald-500/20">
                    M
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white">Safaricom M-Pesa</h4>
                    <p class="text-[10px] text-slate-400">Direct STK Push</p>
                </div>
            </div>
            <span class="text-sm font-black font-mono text-emerald-400">{{ number_format($metrics['mpesaRevenue']) }} <span class="text-[10px] text-slate-400 font-sans">KES</span></span>
        </div>

        <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-400 font-bold text-xs border border-cyan-500/20">
                    P
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white">PayHero Gateway</h4>
                    <p class="text-[10px] text-slate-400">Fast Mobile Money</p>
                </div>
            </div>
            <span class="text-sm font-black font-mono text-cyan-400">{{ number_format($metrics['payheroRevenue']) }} <span class="text-[10px] text-slate-400 font-sans">KES</span></span>
        </div>

        <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 font-bold text-xs border border-indigo-500/20">
                    F
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white">Flutterwave</h4>
                    <p class="text-[10px] text-slate-400">East Africa & Cards</p>
                </div>
            </div>
            <span class="text-sm font-black font-mono text-indigo-400">{{ number_format($metrics['flutterwaveRevenue']) }} <span class="text-[10px] text-slate-400 font-sans">KES</span></span>
        </div>
    </div>

    <!-- SUBSCRIPTIONS MANAGEMENT TABLE -->
    <div class="p-6 rounded-3xl glass-panel space-y-4">
        <!-- FILTER BAR -->
        <form method="GET" action="{{ route('admin.revenue.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 pb-2 border-b border-white/5">
            <div class="flex flex-wrap items-center gap-2">
                <!-- Status Pills -->
                <a href="{{ route('admin.revenue.index', array_merge(request()->query(), ['status' => 'all'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ ($filters['status'] === 'all') ? 'bg-white/15 text-white border border-white/20' : 'bg-white/5 text-slate-400 hover:text-white' }}">
                    All
                </a>
                <a href="{{ route('admin.revenue.index', array_merge(request()->query(), ['status' => 'active'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ ($filters['status'] === 'active') ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-white/5 text-slate-400 hover:text-white' }}">
                    Active VIP ({{ $metrics['activeSubscribers'] }})
                </a>
                <a href="{{ route('admin.revenue.index', array_merge(request()->query(), ['status' => 'pending'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ ($filters['status'] === 'pending') ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-white/5 text-slate-400 hover:text-white' }}">
                    Pending ({{ $metrics['pendingCount'] }})
                </a>
                <a href="{{ route('admin.revenue.index', array_merge(request()->query(), ['status' => 'expired'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ ($filters['status'] === 'expired') ? 'bg-slate-700/40 text-slate-300 border border-white/10' : 'bg-white/5 text-slate-400 hover:text-white' }}">
                    Expired
                </a>

                <!-- Gateway Dropdown -->
                <select name="gateway" onchange="this.form.submit()" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-amber-400">
                    <option value="all" {{ $filters['gateway'] === 'all' ? 'selected' : '' }}>All Gateways</option>
                    <option value="mpesa" {{ $filters['gateway'] === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                    <option value="payhero" {{ $filters['gateway'] === 'payhero' ? 'selected' : '' }}>PayHero</option>
                    <option value="flutterwave" {{ $filters['gateway'] === 'flutterwave' ? 'selected' : '' }}>Flutterwave</option>
                </select>

                <!-- Plan Dropdown -->
                <select name="plan" onchange="this.form.submit()" class="bg-[#12131A] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-amber-400">
                    <option value="all" {{ $filters['plan'] === 'all' ? 'selected' : '' }}>All Periods</option>
                    <option value="week" {{ $filters['plan'] === 'week' ? 'selected' : '' }}>1 Week Pass</option>
                    <option value="month" {{ $filters['plan'] === 'month' ? 'selected' : '' }}>1 Month Pass</option>
                </select>
            </div>

            <!-- Search Field -->
            <div class="relative w-full md:w-64">
                <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Search user, phone, ref..." class="w-full bg-[#12131A] border border-white/10 rounded-xl pl-8 pr-3 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-400">
                <svg class="w-3.5 h-3.5 text-slate-500 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </form>

        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-white/10 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-3">Subscriber / Account</th>
                        <th class="py-3 px-3">Plan / Period</th>
                        <th class="py-3 px-3">Gateway & Ref</th>
                        <th class="py-3 px-3 text-right">Amount</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3">Activated / Expires</th>
                        <th class="py-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-300">
                    @forelse($subscriptions as $sub)
                        @php
                            $isActive = $sub->isActive();
                            $receipt = $sub->metadata['receipt'] ?? $sub->metadata['verify']['provider_reference'] ?? $sub->metadata['verify']['third_party_reference'] ?? null;
                            $daysLeft = ($sub->expires_at && $sub->expires_at->isFuture()) ? now()->diffInDays($sub->expires_at) : 0;
                        @endphp
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <!-- Subscriber / Account -->
                            <td class="py-3 px-3">
                                <div class="font-bold text-white flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full {{ $isActive ? 'bg-emerald-400' : 'bg-slate-600' }}"></span>
                                    <span>{{ $sub->username }}</span>
                                </div>
                                @if($sub->phone)
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $sub->phone }}</div>
                                @endif
                                <div class="text-[10px] text-slate-500 font-mono truncate max-w-[140px]">{{ $sub->device_id }}</div>
                            </td>

                            <!-- Plan / Period -->
                            <td class="py-3 px-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ str_contains($sub->package_id, 'week') ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20' }}">
                                    {{ str_contains($sub->package_id, 'week') ? '1 Week (7D)' : '1 Month (30D)' }}
                                </span>
                                <div class="text-[10px] text-slate-400 mt-1 font-mono">
                                    {{ $sub->period_days ?: 30 }} Days Pass
                                </div>
                            </td>

                            <!-- Gateway & Ref -->
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-bold uppercase tracking-wider {{ $sub->gateway === 'mpesa' ? 'bg-emerald-500/20 text-emerald-300' : ($sub->gateway === 'payhero' ? 'bg-cyan-500/20 text-cyan-300' : 'bg-indigo-500/20 text-indigo-300') }}">
                                        {{ $sub->gateway }}
                                    </span>
                                    @if($receipt)
                                        <span class="font-mono text-[10px] font-bold text-amber-300 bg-amber-500/10 px-1 rounded">{{ $receipt }}</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5 truncate max-w-[160px]" title="{{ $sub->reference }}">
                                    {{ $sub->reference }}
                                </div>
                            </td>

                            <!-- Amount -->
                            <td class="py-3 px-3 text-right">
                                <div class="font-black text-white font-mono text-sm">
                                    {{ number_format($sub->amount) }} <span class="text-[10px] text-slate-400 font-sans">{{ $sub->currency }}</span>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-3 text-center">
                                @if($isActive)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        ACTIVE VIP
                                    </span>
                                    <div class="text-[9px] text-emerald-400 font-bold mt-0.5">{{ $daysLeft }}d left</div>
                                @elseif($sub->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        Pending
                                    </span>
                                @elseif($sub->status === 'failed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                        Failed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-700/40 text-slate-400 border border-white/5">
                                        Expired
                                    </span>
                                @endif
                            </td>

                            <!-- Activated / Expires -->
                            <td class="py-3 px-3">
                                @if($sub->starts_at)
                                    <div class="text-[11px] text-slate-200">{{ $sub->starts_at->format('d M Y, H:i') }}</div>
                                @else
                                    <div class="text-[11px] text-slate-500">Created {{ $sub->created_at->format('d M Y') }}</div>
                                @endif

                                @if($sub->expires_at)
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        Exp: <span class="{{ $isActive ? 'text-amber-300 font-semibold' : 'text-slate-500' }}">{{ $sub->expires_at->format('d M Y') }}</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if(!$isActive)
                                        <form action="{{ route('admin.revenue.activate', $sub->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Activate VIP for {{ $sub->username }}?')" class="px-2.5 py-1 rounded-lg bg-emerald-500/15 text-emerald-300 hover:bg-emerald-500/25 border border-emerald-500/30 text-[10px] font-bold transition-all">
                                                Activate
                                            </button>
                                        </form>
                                    @else
                                        <!-- Extend +30 Days -->
                                        <form action="{{ route('admin.revenue.extend', $sub->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="days" value="30">
                                            <button type="submit" title="Extend +30 Days" class="px-2 py-1 rounded-lg bg-amber-500/15 text-amber-300 hover:bg-amber-500/25 border border-amber-500/30 text-[10px] font-bold transition-all">
                                                +30d
                                            </button>
                                        </form>
                                        <!-- Cancel -->
                                        <form action="{{ route('admin.revenue.cancel', $sub->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Revoke VIP access for {{ $sub->username }}?')" title="Revoke VIP" class="px-2 py-1 rounded-lg bg-rose-500/15 text-rose-300 hover:bg-rose-500/25 border border-rose-500/30 text-[10px] font-bold transition-all">
                                                Revoke
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">
                                No subscription records match your filters.
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
