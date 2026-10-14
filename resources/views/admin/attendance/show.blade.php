<!-- TODO(UI) -->
@extends('layouts.app', ['title' => 'Presensi Sesi: ' . $session->title])

@section('content')
    <h1>Detail & Pengisian Presensi Sesi</h1>

    <p>
        <a href="{{ route('admin.attendance-sessions.index') }}">&larr; Kembali ke Daftar Sesi</a> |
        <a href="{{ route('admin.attendance-sessions.edit', $session) }}">Edit Sesi</a>
    </p>

    {{-- Detail Sesi Card --}}
    <table border="1" cellpadding="6" cellspacing="0" style="margin-bottom: 20px;">
        <tbody>
            <tr>
                <th>Judul Sesi</th>
                <td><strong>{{ $session->title }}</strong></td>
                <th>Tipe Kegiatan</th>
                <td><strong>{{ $session->session_type->label() }}</strong></td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td>{{ $session->date->format('l, d F Y') }}</td>
                <th>Waktu</th>
                <td>
                    @if ($session->start_time)
                        {{ substr($session->start_time, 0, 5) }} {{ $session->end_time ? '- ' . substr($session->end_time, 0, 5) : '' }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th>Lokasi</th>
                <td>{{ $session->location ?? '-' }}</td>
                <th>Status Presensi</th>
                <td>
                    @if ($session->is_open)
                        <span style="color: green; font-weight: bold;">Terbuka (Dapat Diisi)</span>
                    @else
                        <span style="color: red; font-weight: bold;">Ditutup (Terkunci)</span>
                    @endif

                    <form action="{{ route('admin.attendance-sessions.toggle-open', $session) }}" method="POST" style="display:inline; margin-left: 10px;">
                        @csrf
                        @method('PATCH')
                        <button type="submit">
                            {{ $session->is_open ? 'Tutup Presensi' : 'Buka Kembali Presensi' }}
                        </button>
                    </form>
                </td>
            </tr>
            @if ($session->description)
                <tr>
                    <th>Agenda / Keterangan</th>
                    <td colspan="3">{{ $session->description }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Ringkasan Statistik Presensi --}}
    <fieldset style="margin-bottom: 20px;">
        <legend><strong>Ringkasan Kehadiran</strong></legend>
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div>Hadir: <strong style="color: green;">{{ $counts['present'] ?? 0 }}</strong></div>
            <div>Terlambat: <strong style="color: darkorange;">{{ $counts['late'] ?? 0 }}</strong></div>
            <div>Izin: <strong style="color: blue;">{{ $counts['excused'] ?? 0 }}</strong></div>
            <div>Sakit: <strong style="color: purple;">{{ $counts['sick'] ?? 0 }}</strong></div>
            <div>Alpa: <strong style="color: red;">{{ $counts['absent'] ?? 0 }}</strong></div>
            <div>Total Terdata: <strong>{{ array_sum($counts) }} / {{ $members->count() }} Anggota</strong></div>
        </div>
    </fieldset>

    {{-- Form Checklist Presensi Siswa --}}
    <h2>Checklist Presensi Anggota</h2>

    @if (! $session->is_open)
        <p style="color: red;">
            <strong>Catatan:</strong> Sesi presensi ini saat ini berstatus <strong>DITUTUP</strong>. Untuk mengubah data kehadiran, silakan klik tombol "Buka Kembali Presensi" di atas.
        </p>
    @endif

    @if ($members->isEmpty())
        <p>Tidak ada anggota aktif yang terdaftar.</p>
    @else
        <form action="{{ route('admin.attendance-sessions.record', $session) }}" method="POST">
            @csrf

            <table border="1" cellpadding="6" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="22%">Nama & NIS Anggota</th>
                        <th width="18%">Talenta & Paduan Suara</th>
                        <th width="32%">Status Kehadiran</th>
                        <th width="24%">Catatan / Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($members as $index => $member)
                        @php
                            $record = $recordsByUser->get($member->id);
                            $selectedStatus = old("attendances.{$index}.status", $record?->status ? ($record->status instanceof \App\Enums\AttendanceStatus ? $record->status->value : $record->status) : \App\Enums\AttendanceStatus::Present->value);
                            $existingNote = old("attendances.{$index}.notes", $record?->notes ?? '');
                            $primaryTalent = $member->musicRoles->firstWhere('pivot.is_primary', true);
                        @endphp
                        <tr>
                            <td align="center">{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $member->name }}</strong>
                                @if ($member->nis)
                                    <br><small>NIS: {{ $member->nis }}</small>
                                @endif
                                @if ($member->class_name)
                                    <small>({{ $member->class_name }})</small>
                                @endif
                                <input type="hidden" name="attendances[{{ $index }}][user_id]" value="{{ $member->id }}">
                            </td>
                            <td>
                                @if ($primaryTalent)
                                    <span>{{ $primaryTalent->name }}</span>
                                    @if ($primaryTalent->pivot->skill_level)
                                        <small>[{{ ucfirst($primaryTalent->pivot->skill_level) }}]</small>
                                    @endif
                                @else
                                    <em>-</em>
                                @endif

                                @if ($member->is_choir)
                                    <br><small style="color: darkcyan;">[Choir]</small>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    @foreach (\App\Enums\AttendanceStatus::cases() as $statusOption)
                                        <label style="cursor: pointer;">
                                            <input type="radio"
                                                   name="attendances[{{ $index }}][status]"
                                                   value="{{ $statusOption->value }}"
                                                   {{ $selectedStatus === $statusOption->value ? 'checked' : '' }}
                                                   {{ ! $session->is_open ? 'disabled' : '' }}>
                                            {{ $statusOption->label() }}
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <input type="text"
                                       name="attendances[{{ $index }}][notes]"
                                       value="{{ $existingNote }}"
                                       placeholder="Keterangan..."
                                       maxlength="255"
                                       style="width: 95%;"
                                       {{ ! $session->is_open ? 'disabled' : '' }}>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <br>
            @if ($session->is_open)
                <div>
                    <button type="submit" style="padding: 8px 16px; font-weight: bold; background-color: #2b6cb0; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Simpan Data Presensi Anggota
                    </button>
                </div>
            @endif
        </form>
    @endif
@endsection
