@extends('layouts.app', ['title' => 'Dashboard Anggota - Harmoni Musik'])

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Member Musician Profile Banner --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 border border-slate-800 p-6 sm:p-8 text-white shadow-xl">
        <div class="absolute -right-8 -bottom-8 opacity-10 pointer-events-none">
            <svg class="w-64 h-64 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
            </svg>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        Member Dashboard &bull; {{ $user->class_name ?? 'Siswa' }} &bull; NIS: {{ $user->nis ?? '-' }}
                    </span>
                    @if ($user->is_choir)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                            <svg class="w-3.5 h-3.5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a.75.75 0 0 0 .632-.723V6.75A.75.75 0 0 0 18.75 6H9a.75.75 0 0 0-.75.75v8.053a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66A.75.75 0 0 0 7.5 13.25V9.75A.75.75 0 0 1 8.25 9h.75Z" />
                            </svg>
                            Tim Paduan Suara Upacara
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Halo, {{ $user->name }}!</h1>
                <p class="text-sm text-slate-300 max-w-xl">
                    Semangat berkarya dan bermusik. Cek jadwal latihan terdekat, pantau instrumen titipan Anda di studio, dan reservasi studio musik untuk band kamu.
                </p>

                {{-- Musical Talents Badges --}}
                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                    <span class="text-xs text-slate-400 mr-1 font-medium">Keahlian Musik:</span>
                    @forelse ($user->musicRoles as $role)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-semibold {{ $role->pivot->is_primary ? 'bg-amber-400 text-slate-950 font-bold' : 'bg-slate-800 text-slate-200 border border-slate-700' }}">
                            {{ $role->name }}
                            @if ($role->pivot->skill_level)
                                <span class="opacity-75 text-[10px]">({{ ucfirst($role->pivot->skill_level) }})</span>
                            @endif
                        </span>
                    @empty
                        <span class="text-xs text-slate-400 italic">Belum memilih instrumen utama.</span>
                    @endforelse
                </div>
            </div>

            {{-- Attendance Gauge --}}
            <div class="flex flex-row md:flex-col items-center justify-center p-4 rounded-xl bg-white/10 backdrop-blur-md border border-white/10 shrink-0 text-center gap-2">
                <div class="relative flex items-center justify-center">
                    <div class="text-3xl font-black text-amber-400 font-mono">{{ $attendanceRate }}%</div>
                </div>
                <div class="text-xs text-slate-300 font-medium">
                    Tingkat Kehadiran Presensi<br>
                    <span class="text-amber-300 font-semibold">({{ $attendedCount }} dari {{ $totalSessions }} Sesi)</span>
                </div>
            </div>
        </div>
    </div>

    {{-- HIGHLIGHT: My Deposited Instruments in Studio --}}
    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900 transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a.75.75 0 0 0 .632-.723V6.75A.75.75 0 0 0 18.75 6H9a.75.75 0 0 0-.75.75v8.053a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66A.75.75 0 0 0 7.5 13.25V9.75A.75.75 0 0 1 8.25 9h.75Z" />
                    </svg>
                    <span>Alat Musik Titipan Saya di Studio</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Kelola status alat musik pribadi yang Anda titipkan di ruang studio sekolah
                </p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 w-fit">
                {{ $myInstruments->count() }} Alat Terdaftar
            </span>
        </div>

        @if ($myInstruments->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-200 dark:border-slate-800 p-8 text-center text-sm text-slate-400">
                <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Anda belum menitipkan alat musik pribadi di studio. Jika menitipkan gitar, biola, atau instrumen pribadi, hubungi pengurus eskul untuk didata ke sistem.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($myInstruments as $item)
                    <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-5 bg-slate-50/50 dark:bg-slate-850 flex flex-col justify-between transition-all hover:border-slate-300 dark:hover:border-slate-700">
                        <div>
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $item->category }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $item->status->badgeClasses() }}">
                                    {{ $item->status->label() }}
                                </span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $item->name }}</h3>
                            <p class="text-xs text-slate-500 mt-1">
                                Posisi Studio: <strong class="text-slate-700 dark:text-slate-300">{{ $item->location_slot ?? 'Studio Musik' }}</strong>
                            </p>
                            @if ($item->condition_notes)
                                <p class="text-xs text-slate-500 italic mt-1.5 bg-white dark:bg-slate-900/60 p-2 rounded-lg border border-slate-100 dark:border-slate-800">
                                    "{{ $item->condition_notes }}"
                                </p>
                            @endif

                            {{-- Current Loan Info if in use --}}
                            @if ($item->isInUse() && $item->currentLoan)
                                <div class="mt-3 p-3 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40 text-xs text-amber-800 dark:text-amber-300">
                                    <p class="font-semibold">Sedang Dipinjam:</p>
                                    <p>{{ $item->currentLoan->borrower->name ?? 'Siswa lain' }} (Sejak {{ $item->currentLoan->borrowed_at->diffForHumans() }})</p>
                                    <p class="text-[11px] opacity-80 mt-1">Alat tidak bisa dibawa pulang sampai peminjam mengembalikan ke studio.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Action Controls for Owner --}}
                        <div class="mt-5 pt-4 border-t border-slate-200 dark:border-slate-800">
                            @if ($item->isAvailable())
                                <form action="{{ route('member.instruments.toggle-take-home', $item) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full py-2 px-3 rounded-lg bg-slate-900 hover:bg-slate-800 text-white dark:bg-slate-800 dark:hover:bg-slate-700 text-xs font-bold transition-colors flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                                        </svg>
                                        <span>Tandai Dibawa Pulang ke Rumah</span>
                                    </button>
                                </form>
                            @elseif ($item->isTakenHome())
                                <form action="{{ route('member.instruments.toggle-take-home', $item) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full py-2 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-sm transition-colors flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        <span>Tandai Sudah Dititipkan Kembali</span>
                                    </button>
                                </form>
                            @elseif ($item->isInUse())
                                <button type="button" disabled class="w-full py-2 px-3 rounded-lg bg-slate-200 dark:bg-slate-800 text-slate-400 text-xs font-semibold cursor-not-allowed">
                                    Sedang Digunakan Siswa Lain
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Two Columns: Schedule & Studio Bookings --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Left: Upcoming Rehearsals & Ceremony Schedule --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900 transition-colors space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Jadwal Latihan & Upacara Terdekat</h2>
                    <p class="text-xs text-slate-500">Sesi forum rutin dan persiapan upacara mendatang</p>
                </div>
                <a href="{{ route('member.attendance') }}" class="text-xs font-semibold text-amber-600 dark:text-amber-400 hover:underline">
                    Lihat Histori &rarr;
                </a>
            </div>

            @if ($upcomingSessions->isEmpty())
                <p class="text-sm text-slate-400 py-6 text-center">Belum ada agenda jadwal latihan baru yang dipublikasikan.</p>
            @else
                <div class="space-y-3">
                    @foreach ($upcomingSessions as $session)
                        <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-850 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $session->title }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $session->session_type->value === 'upacara' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' }}">
                                        {{ $session->session_type->label() }}
                                    </span>
                                </div>
                                <div class="text-xs text-slate-500 space-y-0.5">
                                    <p>📅 {{ $session->date->translatedFormat('l, d F Y') }}</p>
                                    <p>⏰ {{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }} WIB &bull; 📍 {{ $session->location ?? 'Studio Musik Sekolah' }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $session->is_open ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-slate-200 dark:bg-slate-800 text-slate-500' }}">
                                {{ $session->is_open ? 'Presensi Dibuka' : 'Sesi Terjadwal' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Right: My Studio Bookings --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900 transition-colors space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Reservasi Studio Musik Saya</h2>
                    <p class="text-xs text-slate-500">Histori dan status persetujuan pemakaian studio</p>
                </div>
            </div>

            @if ($myBookings->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-200 dark:border-slate-800 p-8 text-center text-sm text-slate-400">
                    <p>Anda belum memiliki jadwal reservasi studio.</p>
                    <p class="text-xs text-slate-500 mt-1">Hubungi pengurus untuk menjadwalkan latihan band atau latihan solo instrumen.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($myBookings as $booking)
                        <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-850 flex items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $booking->room->name }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $booking->status->badgeClasses() }}">
                                        {{ $booking->status->label() }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    {{ $booking->booking_date->translatedFormat('d M Y') }} &bull; {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }} WIB
                                </p>
                                <p class="text-xs text-slate-600 dark:text-slate-400 italic mt-0.5">"{{ $booking->purpose ?? 'Latihan Mandiri' }}"</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Bottom Section: Studio Available Instruments & Upcoming School Events --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Available Gear in Studio --}}
        <div class="lg:col-span-2 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900 transition-colors">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Instrumen Musik yang Siap Dipakai di Studio</h2>
            <p class="text-xs text-slate-500 mb-4">Daftar instrumen milik eskul dan titipan siswa yang berstatus 'Tersedia' di studio</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach ($availableInstruments as $inst)
                    <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-850 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between text-[10px] text-slate-400 font-semibold uppercase">
                                <span>{{ $inst->category }}</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">&bull; Ready</span>
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white mt-1">{{ $inst->name }}</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">{{ $inst->location_slot ?? 'Studio' }}</p>
                        </div>
                        <div class="mt-2 pt-2 border-t border-slate-200/60 dark:border-slate-800 text-[10px] text-slate-400">
                            {{ $inst->isStudentOwned() ? 'Milik: ' . ($inst->owner->name ?? 'Siswa') : 'Milik Eskul' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Events & Festivals --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900 transition-colors">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Agenda Pentas Seni & Lomba</h2>
            <p class="text-xs text-slate-500 mb-4">Event sekolah dan festival mendatang</p>

            <div class="space-y-3">
                @foreach ($upcomingEvents as $ev)
                    <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-850">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $ev->title }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $ev->event_type->badgeClasses() }}">
                                {{ $ev->event_type->label() }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500">{{ $ev->start_date->translatedFormat('d F Y') }}</p>
                        <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-1 line-clamp-2">{{ $ev->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
