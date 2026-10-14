<!-- TODO(UI) -->
@extends('layouts.app', ['title' => 'Edit Sesi: ' . $session->title])

@section('content')
    <h1>Edit Sesi Presensi: {{ $session->title }}</h1>
    <p>
        <a href="{{ route('admin.attendance-sessions.show', $session) }}">&larr; Kembali ke Detail Sesi</a> |
        <a href="{{ route('admin.attendance-sessions.index') }}">Daftar Sesi</a>
    </p>

    <form action="{{ route('admin.attendance-sessions.update', $session) }}" method="POST">
        @include('admin.attendance._form', ['isEdit' => true, 'session' => $session])
    </form>
@endsection
