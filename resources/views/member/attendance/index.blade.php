<!-- TODO(UI) -->
@extends('layouts.app', ['title' => 'Presensi & Jadwal Latihan Saya'])

@section('content')
    <h1>Presensi & Jadwal Kegiatan Musik</h1>
    <p>Halo, <strong>{{ $user->name }}</strong>! Berikut adalah statistik kehadiran dan agenda kegiatan musik Anda.</p>

    {{-- Statistik Kehadiran Member --}}
    <fieldset style="margin-bottom: 25px;">
        <legend><strong>Statistik Kehadiran Saya</strong></legend>
        <div style="display: flex; gap: 25px; flex-wrap: wrap; align-items: center;">
            <div style="font-size: 1.2em;">
                Tingkat Kehadiran: <strong style="color: {{ $stats['rate'] >= 75 ? 'green' : 'orange' }};">{{ $stats['rate'] }}%</strong>
            </div>
            <div>Total Sesi Selesai: <strong>{{ $stats['total_sessions'] }}</strong></div>
            <div>Hadir / Tepat Waktu: <strong style="color: green;">{{ $stats['attended'] }}</strong></div>
            <div>Izin: <strong style="color: blue;">{{ $stats['excused'] }}</strong></div>
            <div>Sakit: <strong style="color: purple;">{{ $stats['sick'] }}</strong></div>
            <div>Alpa: <strong style="color: red;">{{ $stats['absent'] }}</strong></div>
        </div>
    </fieldset>

    {{-- Agenda / Sesi Latihan Mendatang --}}
    <h2>Jadwal Sesi Mendatang</h2>
    @if ($upcomingSessions->isEmpty())
        <p>Belum ada jadwal sesi latihan atau upacara mendatang.</p>
    @else
        <table border="1" cellpadding="6" cellspacing="0" width="100%" style="margin-bottom: 25px;">
            <thead>
                <tr>
                    <th>Agenda / Kegiatan</th>
                    <th>Tipe Sesi</th>
                    <th>Tanggal & Waktu</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($upcomingSessions as $upcoming)
                    <tr>
                        <td>
                            <strong>{{ $upcoming->title }}</strong>
                            @if ($upcoming->description)
                                <br><small>{{ $upcoming->description }}</small>
                            @endif
                        </td>
                        <td align="center">{{ $upcoming->session_type->label() }}</td>
                        <td align="center">
                            {{ $upcoming->date->format('l, d M Y') }}
                            @if ($upcoming->start_time)
                                <br><small>{{ substr($upcoming->start_time, 0, 5) }} {{ $upcoming->end_time ? '- ' . substr($upcoming->end_time, 0, 5) : '' }}</small>
                            @endif
                        </td>
                        <td>{{ $upcoming->location ?? '-' }}</td>
                        <td align="center">
                            @if ($upcoming->is_open)
                                <span style="color: green;">Terjadwal</span>
                            @else
                                <span style="color: gray;">Ditutup</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Riwayat Presensi Siswa --}}
    <h2>Riwayat Kehadiran Saya</h2>
    @if ($records->isEmpty())
        <p>Belum ada riwayat kehadiran yang tercatat untuk akun Anda.</p>
    @else
        <table border="1" cellpadding="6" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kegiatan / Sesi</th>
                    <th>Tipe Sesi</th>
                    <th>Status Kehadiran</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td align="center">{{ $record->session->date->format('d M Y') }}</td>
                        <td>
                            <strong>{{ $record->session->title }}</strong>
                            @if ($record->session->location)
                                <br><small>Lokasi: {{ $record->session->location }}</small>
                            @endif
                        </td>
                        <td align="center">{{ $record->session->session_type->label() }}</td>
                        <td align="center">
                            @if ($record->status->value === 'present')
                                <strong style="color: green;">Hadir</strong>
                            @elseif ($record->status->value === 'late')
                                <strong style="color: darkorange;">Terlambat</strong>
                            @elseif ($record->status->value === 'excused')
                                <strong style="color: blue;">Izin</strong>
                            @elseif ($record->status->value === 'sick')
                                <strong style="color: purple;">Sakit</strong>
                            @else
                                <strong style="color: red;">Alpa</strong>
                            @endif
                        </td>
                        <td>{{ $record->notes ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 15px;">
            {{ $records->links() }}
        </div>
    @endif
@endsection
