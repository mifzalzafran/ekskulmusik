<!-- TODO(UI) -->
@php
    $isEdit = $isEdit ?? (isset($session) && $session?->exists);
    $selectedType = old('session_type', isset($session) ? ($session->session_type instanceof \App\Enums\AttendanceSessionType ? $session->session_type->value : $session->session_type) : \App\Enums\AttendanceSessionType::Latihan->value);
    $dateValue = old('date', isset($session) && $session?->date ? ($session->date instanceof \Carbon\CarbonInterface ? $session->date->format('Y-m-d') : substr((string) $session->date, 0, 10)) : now()->toDateString());
    $isOpenChecked = old('_token') !== null
        ? (bool) old('is_open')
        : (bool) ($session->is_open ?? true);
@endphp

@csrf
@if ($isEdit)
    @method('PUT')
@endif

<div>
    <label for="title">Judul / Agenda Sesi *</label><br>
    <input type="text" name="title" id="title" value="{{ old('title', $session->title ?? '') }}" required maxlength="150" style="width: 100%; max-width: 450px;">
    @error('title')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<div>
    <label for="session_type">Tipe Sesi *</label><br>
    <select name="session_type" id="session_type" required>
        @foreach (\App\Enums\AttendanceSessionType::cases() as $typeOption)
            <option value="{{ $typeOption->value }}" {{ $selectedType === $typeOption->value ? 'selected' : '' }}>
                {{ $typeOption->label() }}
            </option>
        @endforeach
    </select>
    @error('session_type')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<div>
    <label for="date">Tanggal Sesi *</label><br>
    <input type="date" name="date" id="date" value="{{ $dateValue }}" required>
    @error('date')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<div style="display: flex; gap: 20px;">
    <div>
        <label for="start_time">Jam Mulai</label><br>
        <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $session->start_time ?? '') }}">
        @error('start_time')
            <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>
    <div>
        <label for="end_time">Jam Selesai</label><br>
        <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $session->end_time ?? '') }}">
        @error('end_time')
            <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>
</div>
<br>

<div>
    <label for="location">Lokasi / Ruangan</label><br>
    <input type="text" name="location" id="location" value="{{ old('location', $session->location ?? '') }}" placeholder="Contoh: Ruang Musik, Studio 1, Lapangan Utama" style="width: 100%; max-width: 450px;">
    @error('location')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<div>
    <label for="description">Catatan / Detail Agenda Latihan</label><br>
    <textarea name="description" id="description" rows="4" style="width: 100%; max-width: 450px;" placeholder="Lagu yang dilatih, partitur yang disiapkan, instruksi khusus...">{{ old('description', $session->description ?? '') }}</textarea>
    @error('description')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<div>
    <label>
        <input type="checkbox" name="is_open" id="is_open" value="1" {{ $isOpenChecked ? 'checked' : '' }}>
        Buka Status Presensi (Aktif & dapat diinput)
    </label>
    @error('is_open')
        <div style="color: red;">{{ $message }}</div>
    @enderror
</div>
<br>

<div>
    <button type="submit">{{ $isEdit ? 'Simpan Perubahan Sesi' : 'Buat Sesi Presensi' }}</button>
    <a href="{{ route('admin.attendance-sessions.index') }}">Batal</a>
</div>
