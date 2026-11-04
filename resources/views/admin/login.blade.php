<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login • MovieCat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-panel {
            background: rgba(27, 29, 40, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="bg-[#0A0B10] text-slate-100 min-h-screen flex items-center justify-center p-4 selection:bg-[#FF2E4C] selection:text-white antialiased relative overflow-hidden">
    <!-- Ambient Background Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-tr from-[#FF2E4C]/20 to-purple-600/10 blur-[120px] pointer-events-none rounded-full"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo Header -->
        <div class="text-center mb-8 space-y-3">
            <img src="{{ asset('images/icon.png') }}" alt="MovieCat Official Logo" class="w-16 h-16 mx-auto rounded-3xl shadow-2xl shadow-[#FF2E4C]/40 hover:scale-105 transition-transform object-cover border border-white/10" />
            <h1 class="text-3xl font-extrabold tracking-tight text-white">MovieCat Admin</h1>
            <p class="text-xs text-slate-400">Restricted Administration & Control Portal</p>
        </div>

        <!-- Login Card -->
        <div class="glass-panel p-8 rounded-3xl shadow-2xl space-y-6">
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Administrator Email</label>
                    <input type="email" name="email" value="{{ old('email', 'admin@moviecat.app') }}" required autofocus placeholder="admin@moviecat.app" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-[#FF2E4C] transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Password</label>
                    <input type="password" name="password" required value="password" placeholder="••••••••" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-[#FF2E4C] transition-colors">
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" value="1" checked class="accent-[#FF2E4C] rounded">
                        <span>Remember session</span>
                    </label>
                    <span class="text-slate-500 font-mono text-[11px]">v1.0.0</span>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-[#FF2E4C] to-[#FF4B64] hover:from-[#E02440] hover:to-[#FF2E4C] text-white font-bold text-sm shadow-lg shadow-[#FF2E4C]/30 transition-all hover:scale-[1.02] active:scale-[0.99] mt-2">
                    Enter Admin Dashboard &rarr;
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-600 mt-6">&copy; {{ date('Y') }} MovieCat Mobile Platform</p>
    </div>
</body>
</html>