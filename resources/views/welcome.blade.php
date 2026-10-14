<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Harmoni Musik & Paduan Suara - Ekstrakurikuler Musik Sekolah</title>

    @fonts

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-slate-950 text-slate-100 antialiased font-sans selection:bg-amber-500 selection:text-slate-950">
    {{-- Header / Navbar --}}
    <header class="sticky top-0 z-50 w-full border-b border-slate-800/80 bg-slate-950/90 backdrop-blur-md">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-tr from-amber-500 to-amber-400 text-slate-950 font-black shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a.75.75 0 0 0 .632-.723V6.75A.75.75 0 0 0 18.75 6H9a.75.75 0 0 0-.75.75v8.053a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66A.75.75 0 0 0 7.5 13.25V9.75A.75.75 0 0 1 8.25 9h.75Z" />
                    </svg>
                </span>
                <div class="flex flex-col">
                    <span class="font-black text-lg tracking-tight text-white">HARMONI<span class="text-amber-400">MUSIK</span></span>
                    <span class="text-[10px] uppercase font-bold tracking-widest text-slate-400">SMA NEGERI &bull; KREASI NADA</span>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-300">
                <a href="#tentang" class="hover:text-amber-400 transition-colors">Tentang Ekskul</a>
                <a href="#divisi" class="hover:text-amber-400 transition-colors">Divisi & Choir</a>
                <a href="#studio" class="hover:text-amber-400 transition-colors">Fasilitas Studio</a>
                <a href="#inventaris" class="hover:text-amber-400 transition-colors">Inventaris & Titipan</a>
                <a href="#agenda" class="hover:text-amber-400 transition-colors">Agenda Acara</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm shadow-md shadow-amber-500/25 transition-all hover:scale-[1.02]">
                            <span>Buka Admin Dashboard</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('member.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm shadow-md shadow-amber-500/25 transition-all hover:scale-[1.02]">
                            <span>Dashboard Saya</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 transition-all hover:scale-[1.02]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span>Masuk Portal</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <section class="relative overflow-hidden pt-12 pb-24 md:pt-20 md:pb-32">
        {{-- Background Gradient Orbs --}}
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[380px] bg-amber-500/10 blur-[130px] rounded-full pointer-events-none"></div>
        <div class="absolute top-1/3 left-1/4 -translate-y-1/2 w-[500px] h-[300px] bg-purple-600/10 blur-[120px] rounded-full pointer-events-none"></div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto space-y-6">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-amber-500/30 bg-amber-500/10 text-amber-300 text-xs sm:text-sm font-semibold shadow-inner">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Portal Resmi Ekstrakurikuler Musik & Paduan Suara</span>
                </div>

                <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-white leading-tight">
                    Harmoni Jiwa, Nada Berprestasi, <span class="bg-gradient-to-r from-amber-400 via-amber-300 to-amber-500 bg-clip-text text-transparent">Panggung Menginspirasi</span>
                </h1>

                <p class="text-base sm:text-lg text-slate-300 leading-relaxed max-w-2xl mx-auto">
                    Pusat aktivitas musisi sekolah: presensi kehadiran forum eskul, tim inti paduan suara upacara bendera, booking studio musik kedap suara, dan inventaris aman titipan alat musik siswa.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                    <a href="#agenda" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-sm shadow-xl shadow-amber-500/25 transition-all hover:scale-[1.02]">
                        Jadwal Latihan & Agenda Acara
                    </a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-800 font-bold text-sm transition-colors">
                        Masuk Portal Anggota / Pengurus
                    </a>
                </div>
            </div>

            {{-- Live Stats Counter --}}
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-4 p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-2xl">
                <div class="text-center p-3">
                    <div class="text-3xl sm:text-4xl font-black text-amber-400 font-mono">{{ $stats['members_count'] }}+</div>
                    <div class="text-xs sm:text-sm text-slate-400 font-semibold mt-1">Musisi Siswa Aktif</div>
                </div>
                <div class="text-center p-3 border-l border-slate-800">
                    <div class="text-3xl sm:text-4xl font-black text-purple-400 font-mono">{{ $stats['choir_count'] }}</div>
                    <div class="text-xs sm:text-sm text-slate-400 font-semibold mt-1">Tim Paduan Suara</div>
                </div>
                <div class="text-center p-3 border-l border-slate-800">
                    <div class="text-3xl sm:text-4xl font-black text-emerald-400 font-mono">{{ $stats['instruments_count'] }}</div>
                    <div class="text-xs sm:text-sm text-slate-400 font-semibold mt-1">Inventaris Alat Studio</div>
                </div>
                <div class="text-center p-3 border-l border-slate-800">
                    <div class="text-3xl sm:text-4xl font-black text-blue-400 font-mono">{{ $stats['studio_rooms_count'] }}</div>
                    <div class="text-xs sm:text-sm text-slate-400 font-semibold mt-1">Studio Musik Kedap Suara</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 1: Divisi Musik & Paduan Suara --}}
    <section id="divisi" class="py-20 border-t border-slate-800/80 bg-slate-900/40">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold text-amber-400 uppercase tracking-widest">Divisi & Peminatan</span>
                <h2 class="text-3xl sm:text-4xl font-black text-white mt-2">Kanal Bakat Musik Sekolah</h2>
                <p class="text-sm text-slate-400 mt-2">Setiap siswa dapat mengeksplorasi potensi musikalnya di berbagai instrumen dan kelompok vokal.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Choir Card --}}
                <div class="rounded-2xl border border-purple-500/20 bg-gradient-to-b from-purple-950/20 to-slate-900/60 p-7 shadow-lg relative overflow-hidden group hover:border-purple-500/40 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a.75.75 0 0 0 .632-.723V6.75A.75.75 0 0 0 18.75 6H9a.75.75 0 0 0-.75.75v8.053a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66A.75.75 0 0 0 7.5 13.25V9.75A.75.75 0 0 1 8.25 9h.75Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Tim Paduan Suara Upacara</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Regu paduan suara resmi sekolah untuk memimpin lagu kebangsaan Indonesia Raya dan Mengheningkan Cipta di setiap upacara bendera serta lomba paduan suara antarsekolah.
                    </p>
                </div>

                {{-- Band & Acoustic Card --}}
                <div class="rounded-2xl border border-amber-500/20 bg-gradient-to-b from-amber-950/20 to-slate-900/60 p-7 shadow-lg relative overflow-hidden group hover:border-amber-500/40 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a.75.75 0 0 0 .632-.723V6.75A.75.75 0 0 0 18.75 6H9a.75.75 0 0 0-.75.75v8.053a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66A.75.75 0 0 0 7.5 13.25V9.75A.75.75 0 0 1 8.25 9h.75Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Band & Ensemble Akustik</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Unit kolaborasi gitar, bass, drum, keyboard, dan vokal untuk persiapan Pentas Seni (SAF), demo ekstrakurikuler, dan festival band pelajar.
                    </p>
                </div>

                {{-- Sound Engineering Card --}}
                <div class="rounded-2xl border border-emerald-500/20 bg-gradient-to-b from-emerald-950/20 to-slate-900/60 p-7 shadow-lg relative overflow-hidden group hover:border-emerald-500/40 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Audio & Sound Engineering</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Pelatihan mixing console audio, penataan mikrofon panggung, routing kabel, dan akustik ruang untuk mendukung kelancaran live sound setiap pertunjukan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 2: Ruang Studio Musik --}}
    <section id="studio" class="py-20 border-t border-slate-800/80">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-12">
                <div>
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-widest">Fasilitas Sekolah</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-white mt-1">Studio Musik & Sistem Booking</h2>
                    <p class="text-sm text-slate-400 mt-1 max-w-xl">
                        Studio kedap suara dengan sistem booking digital tanpa tabrakan jadwal. Siapa yang pakai, durasi, dan keperluannya tercatat rapi.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($studioRooms as $room)
                    <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 sm:p-8 flex flex-col justify-between hover:border-slate-700 transition-colors">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                    Kapasitas {{ $room->capacity }} Orang
                                </span>
                                <span class="text-xs font-semibold text-emerald-400 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                    Ruangan Aktif
                                </span>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-2">{{ $room->name }}</h3>
                            <p class="text-sm text-slate-300 leading-relaxed mb-6">{{ $room->description }}</p>
                        </div>
                        <div class="pt-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                            <span>Sistem Validasi Anti-Bentrok Waktu</span>
                            <a href="{{ route('login') }}" class="font-bold text-amber-400 hover:underline">Reservasi &rarr;</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Section 3: Inventaris & Logika Titipan Siswa --}}
    <section id="inventaris" class="py-20 border-t border-slate-800/80 bg-slate-900/40">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-widest">Keamanan & Transparansi</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-white leading-tight">
                        Pencatatan Alat Musik Eskul & Titipan Siswa
                    </h2>
                    <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                        Siswa tidak perlu repot membawa instrumen berat (gitar elektrik, biola, saxophone) bolak-balik setiap hari. Anda dapat menitipkannya di rak/loker studio sekolah dengan status ketersediaan yang transparan:
                    </p>

                    <div class="space-y-4">
                        <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-slate-900/80 border border-slate-800">
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shrink-0">
                                Tersedia
                            </span>
                            <p class="text-xs text-slate-300">
                                Alat berada di studio dan dapat dipinjam/digunakan oleh sesama anggota di dalam ruang musik.
                            </p>
                        </div>

                        <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-slate-900/80 border border-slate-800">
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 shrink-0">
                                Dipinjam
                            </span>
                            <p class="text-xs text-slate-300">
                                Sedang dipakai oleh siswa lain di studio. Sistem mencatat siapa peminjamnya dan jam mulai pinjam.
                            </p>
                        </div>

                        <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-slate-900/80 border border-slate-800">
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-zinc-700 text-zinc-300 shrink-0">
                                Dibawa Pulang
                            </span>
                            <p class="text-xs text-slate-300">
                                Khusus alat pribadi jika pemilik membawa pulang ke rumah. Alat tidak bisa dipinjam siswa lain selama status ini aktif.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6 sm:p-8 shadow-2xl relative">
                    <h4 class="text-xs font-bold text-amber-400 uppercase tracking-widest mb-4">Simulasi Data Status Alat Musik</h4>
                    <div class="space-y-3 font-mono text-xs">
                        <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-white">Gitar Akustik Yamaha F310</span>
                                <span class="block text-[11px] text-purple-400 font-sans mt-0.5">Milik: Budi (XI-IPA-1)</span>
                            </div>
                            <span class="px-2.5 py-1 rounded text-[11px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                Dipinjam di Studio
                            </span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-white">Drum Kit Pearl Export</span>
                                <span class="block text-[11px] text-indigo-400 font-sans mt-0.5">Aset Tetap Eskul Sekolah</span>
                            </div>
                            <span class="px-2.5 py-1 rounded text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                Tersedia (Ready)
                            </span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-white">Biola Stentor Student II</span>
                                <span class="block text-[11px] text-purple-400 font-sans mt-0.5">Milik: Siti (X-A)</span>
                            </div>
                            <span class="px-2.5 py-1 rounded text-[11px] font-bold bg-zinc-800 text-zinc-400 border border-zinc-700">
                                Dibawa Pulang
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 4: Agenda Acara Mendatang --}}
    <section id="agenda" class="py-20 border-t border-slate-800/80">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold text-amber-400 uppercase tracking-widest">Penampilan & Aksi Panggung</span>
                <h2 class="text-3xl sm:text-4xl font-black text-white mt-2">Agenda Acara Mendatang</h2>
                <p class="text-sm text-slate-400 mt-2">Daftar festival, pentas seni sekolah (SAF), dan kompetisi paduan suara.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse ($upcomingEvents as $evt)
                    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 flex flex-col justify-between hover:border-amber-500/30 transition-colors">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold {{ $evt->event_type->badgeClasses() }}">
                                    {{ $evt->event_type->label() }}
                                </span>
                                <span class="text-xs font-mono font-semibold text-amber-400">
                                    {{ $evt->start_date->translatedFormat('d M Y') }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">{{ $evt->title }}</h3>
                            <p class="text-xs text-slate-400 mb-3 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                <span>{{ $evt->location }}</span>
                            </p>
                            <p class="text-xs text-slate-300 leading-relaxed">{{ $evt->description }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-slate-500 text-sm">
                        Belum ada agenda acara baru yang dipublikasikan saat ini.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-slate-800 bg-slate-950 py-12 text-slate-500 text-xs">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-black text-sm text-white">HARMONI<span class="text-amber-400">MUSIK</span></span>
                <span>&bull; &copy; {{ date('Y') }} Ekstrakurikuler Musik & Paduan Suara Sekolah.</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('login') }}" class="font-bold text-amber-400 hover:underline">Login Anggota / Pengurus</a>
            </div>
        </div>
    </footer>
</body>
</html>
