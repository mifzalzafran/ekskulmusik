<!-- TODO(UI) -->
@extends('layouts.app', ['title' => 'Buat Sesi Presensi Baru'])

@section('content')
    <h1>Buat Sesi Presensi / Latihan Baru</h1>
    <p>
        <a href="{{ route('admin.attendance-sessions.index') }}">&larr; Kembali ke Daftar Presensi</a>
    </p>

    <form action="{{ route('admin.attendance-sessions.store') }}" method="POST">
        @include('admin.attendance._form', ['isEdit' => false])
    </form>
@endsection
