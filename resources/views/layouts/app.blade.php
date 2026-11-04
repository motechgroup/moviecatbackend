<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'MovieCat Admin Console' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
        code, pre { font-family: 'JetBrains Mono', monospace; }
        .glass-panel {
            background: rgba(22, 24, 34, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .nav-active {
            background: linear-gradient(90deg, rgba(255, 46, 76, 0.22) 0%, rgba(255, 46, 76, 0.06) 100%);
            border-left: 3px solid #FF2E4C;
            color: #FFFFFF !important;
            font-weight: 700 !important;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
</head>
<body class="bg-[#0A0B10] text-slate-100 min-h-screen selection:bg-[#FF2E4C] selection:text-white antialiased">
    <div id="app-container" class="min-h-screen flex">
        
        <!-- MOBILE BACKDROP -->
        <div id="sidebar-backdrop" onclick="toggleSidebar(false)" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"></div>

        <!-- CONSISTENT SIDEBAR NAVIGATION -->
        <aside id="sidebar-menu" class="fixed top-0 bottom-0 left-0 w-72 bg-[#10121A] border-r border-white/5 z-50 flex flex-col justify-between transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none">
            
            <!-- BRAND + NAV ITEMS -->
            <div class="flex flex-col flex-1 overflow-y-auto">
                <!-- Brand Header -->
                <div class="h-20 px-6 flex items-center justify-between border-b border-white/5">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                        <img src="{{ asset('images/icon.png') }}" alt="MovieCat Official Logo" class="w-10 h-10 rounded-2xl shadow-lg shadow-[#FF2E4C]/30 group-hover:scale-105 transition-transform object-cover" />
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-lg font-extrabold tracking-tight text-white">MovieCat</span>
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-[#FF2E4C]/20 text-[#FF2E4C] border border-[#FF2E4C]/30">ADMIN</span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono">Control Center v1.0</span>
                        </div>
                    </a>

                    <button type="button" onclick="toggleSidebar(false)" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="px-3 py-6 space-y-1.5 flex-1">
                    <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Main Menus</div>
                    
                    <!-- 1. Dashboard Overview -->
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/[0.04] transition-all {{ request()->routeIs('admin.dashboard') ? 'nav-active' : '' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-[#FF2E4C]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span>Dashboard Overview</span>
                    </a>

                    <!-- 7. Administrator Profile -->
                    <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/[0.04] transition-all {{ request()->routeIs('admin.profile*') ? 'nav-active' : '' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.profile*') ? 'text-[#FF2E4C]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Admin Profile</span>
                    </a>

                    <!-- 2. Active Devices Fleet -->
                    <a href="{{ route('admin.devices.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/[0.04] transition-all {{ request()->routeIs('admin.devices.*') ? 'nav-active' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.devices.*') ? 'text-emerald-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span>Active Devices Fleet</span>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    </a>

                    <!-- 3. AdMob Monetization -->
                    <a href="{{ route('admin.admob.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/[0.04] transition-all {{ request()->routeIs('admin.admob.*') ? 'nav-active' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.admob.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>AdMob Monetization</span>
                        </div>
                        @if(\App\Models\AppSetting::get('ads_enabled', true))
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">ON</span>
                        @else
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">OFF</span>
                        @endif
                    </a>

                    

                                        <!-- App Error Telemetry Logs -->
                    <a href="{{ route('admin.errors.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/[0.04] transition-all {{ request()->routeIs('admin.errors.*') ? 'nav-active' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.errors.*') ? 'text-rose-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>App Error Logs</span>
                        </div>
                        @php
                            $unresolvedCount = \App\Models\AppError::where('resolved', false)->count();
                        @endphp
                        @if($unresolvedCount > 0)
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">{{ $unresolvedCount }}</span>
                        @endif
                    </a>

                    <!-- 5. Watch History Sync Telemetry -->
                    <a href="{{ route('admin.sync.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/[0.04] transition-all {{ request()->routeIs('admin.sync.*') ? 'nav-active' : '' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.sync.*') ? 'text-purple-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Watch Progress Stream</span>
                    </a>

                    <!-- Downloads & Offline Tracking -->
                    <a href="{{ route('admin.downloads.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.downloads.*') ? 'bg-gradient-to-r from-rose-500/20 to-orange-500/10 text-white border-l-2 border-rose-500' : 'text-slate-300 hover:text-white hover:bg-white/[0.04]' }}">
                        <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Downloads & Offline</span>
                    </a>

                    <!-- 6. Remote App Settings -->
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/[0.04] transition-all {{ request()->routeIs('admin.settings.*') ? 'nav-active' : '' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.settings.*') ? 'text-indigo-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Remote App Config</span>
                    </a>

                    <div class="pt-5 pb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">External Links</div>

                    <!-- API Config Live Link -->
                    <a href="/api/v1/config" target="_blank" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/[0.04] transition-all">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                            </svg>
                            <span>Live Config JSON</span>
                        </div>
                        <span class="text-[10px] font-mono text-slate-400">↗</span>
                    </a>
                </nav>
            </div>

            <!-- USER PROFILE & LOGOUT -->
            <div class="p-4 border-t border-white/5 space-y-3 bg-[#0C0E14]">
                <a href="{{ route('admin.profile') }}" class="flex items-center justify-between p-2 rounded-xl hover:bg-white/5 transition-all group" title="Manage Administrator Profile">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#FF2E4C] to-[#FF6B6B] flex items-center justify-center text-white font-bold text-xs shadow-md shadow-[#FF2E4C]/20 group-hover:scale-105 transition-transform">
                            {{ substr(Auth::user()->name ?? 'Admin', 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <div class="text-xs font-bold text-white group-hover:text-rose-400 transition-colors truncate">{{ Auth::user()->name ?? 'Administrator' }}</div>
                            <div class="text-[10px] text-slate-400 font-mono truncate">{{ Auth::user()->email ?? 'admin@moviecat.app' }}</div>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-slate-500 group-hover:text-slate-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                    </div>
                </div>

                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2 px-3 rounded-xl bg-white/5 hover:bg-rose-500/15 text-slate-300 hover:text-rose-300 border border-white/5 hover:border-rose-500/30 text-xs font-semibold transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN WRAPPER -->
        <div class="flex-1 lg:pl-72 flex flex-col min-h-screen">
            <!-- HEADER -->
            <header class="sticky top-0 z-30 h-16 glass-panel border-b border-white/5 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="toggleSidebar(true)" class="lg:hidden p-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="text-sm font-bold text-white tracking-tight flex items-center gap-2">
                        <span>MovieCat Console</span>
                        <span class="text-slate-400 text-xs font-normal">/</span>
                        <span class="text-slate-300 text-xs font-medium">{{ $title ?? 'Dashboard' }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    
                    <!-- Quick Downloads master switch in header -->
                    <form action="{{ route('admin.downloads.toggle') }}" method="POST" class="inline">
                        @csrf
                        @if(\App\Models\AppSetting::get('feature_downloads_enabled', true))
                            <button type="submit" title="Click to disable all mobile downloads" class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/25 transition-all">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Downloads Active</span>
                            </button>
                        @else
                            <button type="submit" title="Click to enable mobile downloads" class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30 hover:bg-rose-500/25 transition-all">
                                <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                <span>Downloads Disabled</span>
                            </button>
                        @endif
                    </form>

                    <!-- Quick AdMob status pill in header -->
                    <form action="{{ route('admin.admob.toggle') }}" method="POST" class="inline">
                        @csrf
                        @if(\App\Models\AppSetting::get('ads_enabled', true))
                            <button type="submit" title="Click to toggle AdMob off" class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/25 transition-all">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>AdMob Active</span>
                            </button>
                        @else
                            <button type="submit" title="Click to toggle AdMob on" class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30 hover:bg-rose-500/25 transition-all">
                                <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                <span>AdMob Disabled</span>
                            </button>
                        @endif
                    </form>

                    <a href="/api/v1/config" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-medium border border-white/10 transition-colors">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-mono">moviecat.djstube.com</span>
                    </a>
                </div>
            </header>

            <!-- MAIN CONTENT -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs font-medium flex items-center justify-between shadow-lg shadow-emerald-500/5">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            {{ session('success') }}
                        </span>
                    </div>
                @endif

                @yield('content')
            </main>

            <footer class="border-t border-white/5 py-4 px-6 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} MovieCat Mobile Platform • Administrator Console
            </footer>
        </div>
    </div>

    <script>
        function toggleSidebar(open) {
            const sidebar = document.getElementById('sidebar-menu');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (open) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    </script>
</body>
</html>