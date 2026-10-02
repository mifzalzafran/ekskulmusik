<!-- TODO(UI) -->
@extends('layouts.app', ['title' => 'Member Dashboard'])

@section('content')
    <h1>Member Dashboard</h1>
    <p>Welcome, {{ $user->name }}!</p>
    <p>Role: {{ $user->role->label() }}</p>
@endsection
