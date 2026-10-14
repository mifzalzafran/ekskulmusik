@extends('layouts.app', ['title' => 'Admin Dashboard - Ekskul Musik'])

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Header Banner --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 border border-slate-800 p-6 sm:p-8 text-white shadow-xl">
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
            <svg class="w-72 h-72 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
            </svg>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                        Admin Dashboard - Admin Control Center
                    </span>
                    <span class="text-xs text-slate-400">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ $user->name }}!</h1>
                <p class="mt-1 text-sm text-slate-300 max-w-2xl">
                    Kelola seluruh aktivitas musik sekolah: presensi forum & paduan suara upacara, jadwal studio, inventaris titipan siswa, buku kas, dan agenda acara.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('admin.attendance-sessions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-sm font-bold shadow-md shadow-amber-500/20 transition-all hover:scale-[1.02]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Buka Sesi Presensi</span>
                </a>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold border border-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                    </svg>
                    <span>Tambah Anggota</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 4 Main KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        {{-- Card 1: Members --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800/80 dark:bg-slate-900 transition-colors">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Anggota Ekskul</span>
                <span class="p-2.5 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">{{ $stats['total_members'] }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">Siswa Aktif</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="text-slate-600 dark:text-slate-400">Tim Paduan Suara (Choir):</span>
                <span class="font-bold text-amber-600 dark:text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-full">{{ $stats['choir_members'] }} Anggota</span>
            </div>
        </div>

        {{-- Card 2: Booking Studio --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800/80 dark:bg-slate-900 transition-colors">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Booking Studio</span>
                <span class="p-2.5 rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-950/50 dark:text-purple-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">{{ $todayBookings->count() }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">Sesi Hari Ini</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="text-slate-600 dark:text-slate-400">Menunggu Approval:</span>
                @if ($stats['pending_bookings_count'] > 0)
                    <span class="font-bold text-rose-600 dark:text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-full animate-pulse">{{ $stats['pending_bookings_count'] }} Permohonan</span>
                @else
                    <span class="text-slate-500 dark:text-slate-400">Semua Terkonfirmasi</span>
                @endif
            </div>
        </div>

        {{-- Card 3: Inventaris & Alat Titipan --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800/80 dark:bg-slate-900 transition-colors">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Inventaris Alat</span>
                <span class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a.75.75 0 0 0 .632-.723V6.75A.75.75 0 0 0 18.75 6H9a.75.75 0 0 0-.75.75v8.053a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66A.75.75 0 0 0 7.5 13.25V9.75A.75.75 0 0 1 8.25 9h.75Z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">{{ $stats['instruments_available'] }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">Tersedia di Studio</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="text-slate-600 dark:text-slate-400">Dipinjam: <strong class="text-amber-600 dark:text-amber-400">{{ $stats['instruments_in_use'] }}</strong></span>
                <span class="text-slate-600 dark:text-slate-400">Dibawa Pulang: <strong class="text-slate-700 dark:text-slate-300">{{ $stats['instruments_taken_home'] }}</strong></span>
            </div>
        </div>

        {{-- Card 4: Saldo Kas --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800/80 dark:bg-slate-900 transition-colors">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Saldo Kas Ekskul</span>
                <span class="p-2.5 rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0 0a60.07 60.07 0 0 0 15.797 2.101c.727.198 1.453-.342 1.453-1.096V14.25m-15.797-2.1c.727-.198 1.453.342 1.453 1.096v.75m0 0a60.07 60.07 0 0 0 15.797 2.101c.727.198 1.453-.342 1.453-1.096V10.5m-15.797-2.1c.727-.198 1.453.342 1.453 1.096v.75m0 0a60.07 60.07 0 0 0 15.797 2.101c.727.198 1.453-.342 1.453-1.096V6.75m0 0A2.25 2.25 0 0 0 19.5 4.5h-15Z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Rp {{ number_format($stats['current_cash'], 0, ',', '.') }}</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="text-slate-600 dark:text-slate-400">Status Keuangan:</span>
                <span class="font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">Sehat & Aktif</span>
            </div>
        </div>
    </div>

    {{-- Main Operational Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left 2 Columns: Studio Booking & Inventory --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Studio Booking Approval Section --}}
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900 transition-colors">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>Permohonan Booking Studio</span>
                            @if ($pendingBookings->isNotEmpty())
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                    {{ $pendingBookings->count() }} Perlu Diproses
                                </span>
                            @endif
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Persetujuan peminjaman ruang latihan musik eskul</p>
                    </div>
                </div>

                @if ($pendingBookings->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-200 dark:border-slate-800 p-8 text-center text-slate-400 text-sm">
                        <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Tidak ada permohonan booking studio yang tertunda. Semua jadwal telah dikonfirmasi.
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @foreach ($pendingBookings as $booking)
                            <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $booking->user->name }}</span>
                                        <span class="text-xs text-slate-500">({{ $booking->user->class_name ?? 'Siswa' }})</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $booking->status->badgeClasses() }}">
                                            {{ $booking->status->label() }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-slate-600 dark:text-slate-400 space-y-0.5">
                                        <p><strong class="text-slate-700 dark:text-slate-300">{{ $booking->room->name }}</strong> &bull; {{ $booking->booking_date->translatedFormat('d M Y') }} ({{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }} WIB)</p>
                                        <p class="italic text-slate-500">"{{ $booking->purpose ?? 'Latihan Mandiri' }}"</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    {{-- Approve Button --}}
                                    <form action="{{ route('admin.studio-bookings.status', $booking) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold shadow-sm transition-colors flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                            <span>Setujui</span>
                                        </button>
                                    </form>

                                    {{-- Reject Button --}}
                                    <form action="{{ route('admin.studio-bookings.status', $booking) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-800 dark:bg-rose-950/60 dark:hover:bg-rose-900 dark:text-rose-300 text-xs font-bold transition-colors">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Today's Studio Usage Schedule --}}
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                        <span>Jadwal Pemakaian Studio Hari Ini</span>
                    </h2>
                    <span class="text-xs font-medium text-slate-500">{{ now()->translatedFormat('d F Y') }}</span>
                </div>

                @if ($todayBookings->isEmpty())
                    <p class="text-sm text-slate-400 py-4 text-center">Belum ada pemakaian studio yang terjadwal untuk hari ini.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="border-b border-slate-200 dark:border-slate-800 text-slate-400 uppercase tracking-wider font-semibold">
                                <tr>
                                    <th class="py-2.5 px-3">Jam</th>
                                    <th class="py-2.5 px-3">Ruangan</th>
                                    <th class="py-2.5 px-3">Peminjam</th>
                                    <th class="py-2.5 px-3">Keperluan</th>
                                    <th class="py-2.5 px-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                                @foreach ($todayBookings as $tb)
                                    <tr>
                                        <td class="py-3 px-3 font-mono font-bold text-amber-600 dark:text-amber-400">
                                            {{ substr($tb->start_time, 0, 5) }} - {{ substr($tb->end_time, 0, 5) }}
                                        </td>
                                        <td class="py-3 px-3 font-semibold text-slate-900 dark:text-white">{{ $tb->room->name }}</td>
                                        <td class="py-3 px-3 text-slate-700 dark:text-slate-300">{{ $tb->user->name }}</td>
                                        <td class="py-3 px-3 text-slate-500 italic">{{ $tb->purpose ?? '-' }}</td>
                                        <td class="py-3 px-3">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $tb->status->badgeClasses() }}">
                                                {{ $tb->status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Ongoing Instrument Usage & Deposited Gear --}}
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Status Inventaris & Alat Musik Sedang Dipakai</h2>
                        <p class="text-xs text-slate-500">Mencakup alat musik milik eskul dan titipan siswa yang sedang berstatus dipinjam</p>
                    </div>
                </div>

                @if ($activeLoans->isEmpty())
                    <p class="text-sm text-slate-400 py-4 text-center">Seluruh instrumen saat ini aman di rak/loker studio (tidak ada peminjaman aktif).</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($activeLoans as $loan)
                            <div class="rounded-xl border border-amber-200 bg-amber-50/50 dark:border-amber-900/40 dark:bg-amber-950/20 p-4">
                                <div class="flex items-start justify-between">
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $loan->instrument->name }}</h3>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $loan->instrument->status->badgeClasses() }}">
                                        Dipinjam
                                    </span>
                                </div>
                                <div class="mt-2 text-xs text-slate-600 dark:text-slate-300 space-y-1">
                                    <p>
                                        Kepemilikan:
                                        @if ($loan->instrument->isStudentOwned())
                                            <span class="font-semibold text-purple-600 dark:text-purple-400">Titipan (Milik: {{ $loan->instrument->owner->name ?? 'Siswa' }})</span>
                                        @else
                                            <span class="font-semibold text-indigo-600 dark:text-indigo-400">Aset Eskul</span>
                                        @endif
                                    </p>
                                    <p>Dipinjam oleh: <strong class="text-slate-900 dark:text-white">{{ $loan->borrower->name }}</strong></p>
                                    <p class="text-[11px] text-slate-500">Dipinjam sejak: {{ $loan->borrowed_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Right 1 Column: Attendance, Cashflow, and Upcoming Events --}}
        <div class="space-y-8">
            {{-- Active Attendance Sessions Widget --}}
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Presensi Aktif Saat Ini</h2>
                    <a href="{{ route('admin.attendance-sessions.index') }}" class="text-xs font-semibold text-amber-600 dark:text-amber-400 hover:underline">
                        Lihat Semua &rarr;
                    </a>
                </div>

                @if ($activeSessions->isEmpty())
                    <p class="text-xs text-slate-400 py-3 text-center">Tidak ada sesi presensi yang sedang terbuka.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($activeSessions as $ses)
                            <div class="p-3.5 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-850">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $ses->title }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                        {{ $ses->session_type->label() }}
                                    </span>
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ $ses->date->translatedFormat('d M Y') }} &bull; {{ $ses->records_count }} Hadir
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('admin.attendance-sessions.show', $ses) }}" class="block w-full text-center px-3 py-1.5 rounded-lg bg-slate-900 text-white dark:bg-slate-800 dark:hover:bg-slate-700 text-xs font-semibold transition-colors">
                                        Buka Lembar Presensi
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Cashflow Summary Widget --}}
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Mutasi Kas Terbaru</h2>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Saldo: Rp {{ number_format($stats['current_cash'], 0, ',', '.') }}</span>
                </div>

                @if ($recentTransactions->isEmpty())
                    <p class="text-xs text-slate-400 py-3 text-center">Belum ada transaksi kas tercatat.</p>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @foreach ($recentTransactions as $tx)
                            <div class="py-2.5 flex items-center justify-between text-xs">
                                <div>
                                    <p class="font-semibold text-slate-800 dark:text-slate-200 truncate max-w-[180px]">{{ $tx->description }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $tx->transaction_date->translatedFormat('d M Y') }} &bull; {{ $tx->category }}</p>
                                </div>
                                <span class="font-bold {{ $tx->type->value === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $tx->type->value === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Upcoming Agenda / Events Widget --}}
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Agenda Acara Mendatang</h2>
                    <span class="text-xs text-slate-400">{{ $upcomingEvents->count() }} Event</span>
                </div>

                @if ($upcomingEvents->isEmpty())
                    <p class="text-xs text-slate-400 py-3 text-center">Belum ada agenda acara baru yang didaftarkan.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($upcomingEvents as $evt)
                            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-850">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $evt->title }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $evt->event_type->badgeClasses() }}">
                                        {{ $evt->event_type->label() }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500">{{ $evt->start_date->translatedFormat('d M Y') }} &bull; {{ $evt->location }}</p>
                                <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-1 font-medium">{{ $evt->participants_count }} Siswa Terdaftar di Lineup</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
