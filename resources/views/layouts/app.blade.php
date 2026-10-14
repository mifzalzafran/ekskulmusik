<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Harmoni Eskul Musik' }} - {{ config('app.name', 'Ekskul Musik') }}</title>

    @fonts

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-full bg-slate-50 text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-100 flex flex-col font-sans">
    {{-- Header / Navigation Bar --}}
    <header class="sticky top-0 z-40 w-full border-b border-slate-200/80 bg-white/95 backdrop-blur-md dark:border-slate-800/80 dark:bg-slate-900/95 transition-colors">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            {{-- Brand Logo --}}
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-amber-500 to-amber-400 text-slate-950 font-black shadow-md shadow-amber-500/20 group-hover:scale-105 transition-transform">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a.75.75 0 0 0 .632-.723V6.75A.75.75 0 0 0 18.75 6H9a.75.75 0 0 0-.75.75v8.053a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66A.75.75 0 0 0 7.5 13.25V9.75A.75.75 0 0 1 8.25 9h.75Z" />
                        </svg>
                    </span>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-base tracking-tight text-slate-900 dark:text-white">HARMONI<span class="text-amber-500">MUSIK</span></span>
                        <span class="text-[10px] uppercase font-semibold tracking-wider text-slate-600 dark:text-slate-300">Ekskul & Paduan Suara</span>
                    </div>
                </a>
            </div>

            {{-- Desktop Navigation Links --}}
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ url('/') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800/60 transition-colors">
                    Beranda
                </a>

                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 font-semibold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800/60' }} transition-colors">
                            Dashboard Admin
                        </a>
                        <a href="{{ route('admin.attendance-sessions.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.attendance-sessions.*') ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 font-semibold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800/60' }} transition-colors">
                            Presensi Latihan
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 font-semibold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800/60' }} transition-colors">
                            Kelola Anggota
                        </a>
                    @elseif (auth()->user()->isMember())
                        <a href="{{ route('member.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('member.dashboard') ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 font-semibold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800/60' }} transition-colors">
                            Dashboard Anggota
                        </a>
                        <a href="{{ route('member.attendance') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('member.attendance') ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 font-semibold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800/60' }} transition-colors">
                            Presensi Saya
                        </a>
                    @endif
                @endauth
            </nav>

            {{-- User Actions / Auth Controls --}}
            <div class="hidden md:flex items-center gap-3">
                @auth
                    <div class="flex items-center gap-2.5 pl-3 border-l border-slate-200 dark:border-slate-800">
                        <div class="flex flex-col text-right">
                            <span class="text-xs font-semibold text-slate-900 dark:text-white leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wide">
                                {{ auth()->user()->role->label() }}
                                @if (auth()->user()->is_choir)
                                    &bull; Choir
                                @endif
                            </span>
                        </div>
                        <a href="{{ route('password.edit') }}" title="Ganti Password" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                            </svg>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="Keluar" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 text-sm font-semibold shadow-sm transition-colors">
                        <span>Masuk Portal</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                @endauth
            </div>

            {{-- Mobile Menu Button --}}
            <div class="flex md:hidden">
                <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800" aria-label="Toggle navigation">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu Dropdown --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200 dark:border-slate-800 px-4 pt-3 pb-4 space-y-2 bg-white dark:bg-slate-900">
            <a href="{{ url('/') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                Beranda
            </a>
            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                        Dashboard Admin
                    </a>
                    <a href="{{ route('admin.attendance-sessions.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                        Presensi Latihan & Upacara
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                        Kelola Anggota
                    </a>
                @elseif (auth()->user()->isMember())
                    <a href="{{ route('member.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                        Dashboard Anggota
                    </a>
                    <a href="{{ route('member.attendance') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                        Presensi Saya
                    </a>
                @endif
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-slate-900 dark:text-white">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-amber-600 dark:text-amber-400">{{ auth()->user()->role->label() }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg">
                            Log Out
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 rounded-lg bg-amber-500 text-slate-950 text-sm font-semibold">
                    Masuk Portal
                </a>
            @endauth
        </div>
    </header>

    {{-- Main Page Content --}}
    <main class="flex-1">
        {{-- Flash Notification Alerts --}}
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-4">
            @if (session('status'))
                <div class="mb-4 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50/90 p-4 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300 shadow-sm">
                    <svg class="h-5 w-5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50/90 p-4 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300 shadow-sm">
                    <svg class="h-5 w-5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50/90 p-4 text-sm text-rose-800 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300 shadow-sm">
                    <svg class="h-5 w-5 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50/90 p-4 text-sm text-rose-800 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300 shadow-sm">
                    <div class="flex items-center gap-2 font-semibold mb-1">
                        <svg class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        <span>Terdapat beberapa kesalahan input:</span>
                    </div>
                    <ul class="list-disc pl-7 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-auto border-t border-slate-200 bg-white py-6 dark:border-slate-800 dark:bg-slate-900/50 transition-colors">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500 dark:text-slate-400">
            <p>&copy; {{ date('Y') }} Harmoni Musik & Paduan Suara Sekolah. Sistem Manajemen Ekstrakurikuler.</p>
        </div>
    </footer>
</body>
</html>
