<!-- TODO(UI) -->
@extends('layouts.app', ['title' => 'Manajemen Presensi Latihan & Kegiatan'])

@section('content')
    <h1>Manajemen Presensi Latihan & Kegiatan</h1>

    <p>
        <a href="{{ route('admin.attendance-sessions.create') }}"><strong>+ Buat Sesi Presensi Baru</strong></a>
    </p>

    {{-- Filter Form --}}
    <fieldset>
        <legend>Filter Sesi</legend>
        <form action="{{ route('admin.attendance-sessions.index') }}" method="GET">
            <div style="display: flex; flex-wrap: wrap; gap: 15px; align-items: center;">
                <div>
                    <label for="search">Cari:</label><br>
                    <input type="text"
                           name="search"
                           id="search"
                           placeholder="Judul, lokasi, agenda..."
                           value="{{ $filters['search'] ?? '' }}">
                </div>

                <div>
                    <label for="type">Tipe Sesi:</label><br>
                    <select name="type" id="type">
                        <option value="">Semua Tipe</option>
                        @foreach (\App\Enums\AttendanceSessionType::cases() as $typeOption)
                            <option value="{{ $typeOption->value }}" {{ ($filters['type'] ?? '') === $typeOption->value ? 'selected' : '' }}>
                                {{ $typeOption->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="is_open">Status Presensi:</label><br>
                    <select name="is_open" id="is_open">
                        <option value="">Semua Status</option>
                        <option value="1" {{ ($filters['is_open'] ?? '') === '1' ? 'selected' : '' }}>Terbuka (Dapat Diinput)</option>
                        <option value="0" {{ ($filters['is_open'] ?? '') === '0' ? 'selected' : '' }}>Ditutup</option>
                    </select>
                </div>

                <div>
                    <br>
                    <button type="submit">Filter</button>
                    <a href="{{ route('admin.attendance-sessions.index') }}">Reset</a>
                </div>
            </div>
        </form>
    </fieldset>
    <br>

    {{-- Sessions Table --}}
    @if ($sessions->isEmpty())
        <p>Belum ada sesi presensi atau latihan yang sesuai filter.</p>
    @else
        <table border="1" cellpadding="6" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul Sesi</th>
                    <th>Tipe Sesi</th>
                    <th>Tanggal & Waktu</th>
                    <th>Lokasi</th>
                    <th>Status Presensi</th>
                    <th>Data Kehadiran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sessions as $session)
                    <tr>
                        <td align="center">{{ $session->id }}</td>
                        <td>
                            <strong>{{ $session->title }}</strong>
                            @if ($session->description)
                                <br><small style="color: gray;">{{ Str::limit($session->description, 50) }}</small>
                            @endif
                        </td>
                        <td align="center">
                            <strong>{{ $session->session_type->label() }}</strong>
                        </td>
                        <td align="center">
                            {{ $session->date->format('d M Y') }}
                            @if ($session->start_time)
                                <br><small>{{ substr($session->start_time, 0, 5) }} {{ $session->end_time ? '- ' . substr($session->end_time, 0, 5) : '' }}</small>
                            @endif
                        </td>
                        <td>{{ $session->location ?? '-' }}</td>
                        <td align="center">
                            @if ($session->is_open)
                                <span style="color: green; font-weight: bold;">Terbuka</span>
                            @else
                                <span style="color: gray;">Ditutup</span>
                            @endif
                        </td>
                        <td align="center">
                            <strong>{{ $session->records_count }}</strong> Siswa
                        </td>
                        <td align="center">
                            <div style="display: flex; gap: 8px; justify-content: center; align-items: center; flex-wrap: wrap;">
                                <a href="{{ route('admin.attendance-sessions.show', $session) }}"><strong>Buka Presensi</strong></a>
                                <a href="{{ route('admin.attendance-sessions.edit', $session) }}">Edit</a>

                                <form action="{{ route('admin.attendance-sessions.toggle-open', $session) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Ubah status buka/tutup presensi">
                                        {{ $session->is_open ? 'Tutup' : 'Buka' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.attendance-sessions.destroy', $session) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus sesi presensi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color: red;" title="Hapus sesi">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 15px;">
            {{ $sessions->links() }}
        </div>
    @endif
@endsection
