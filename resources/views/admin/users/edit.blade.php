<!-- TODO(UI) -->
@extends('layouts.app', ['title' => 'Edit User: ' . $user->name])

@section('content')
    <h1>Edit User: {{ $user->name }}</h1>

    <p>
        <a href="{{ route('admin.users.index') }}">&larr; Back to Users List</a> |
        <a href="{{ route('admin.users.show', $user) }}">View Details</a>
    </p>

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @include('admin.users._form', ['user' => $user, 'isEdit' => true])
    </form>
@endsection
