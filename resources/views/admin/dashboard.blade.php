<!-- TODO(UI) -->
@extends('layouts.app', ['title' => 'Admin Dashboard'])

@section('content')
    <h1>Admin Dashboard</h1>
    <p>Welcome, {{ $user->name }}!</p>
    <p>Role: {{ $user->role->label() }}</p>
@endsection
