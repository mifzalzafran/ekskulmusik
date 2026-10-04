<!-- TODO(UI) -->
@extends('layouts.app', ['title' => 'Create User - Admin'])

@section('content')
    <h1>Create New User</h1>

    <p><a href="{{ route('admin.users.index') }}">&larr; Back to Users List</a></p>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @include('admin.users._form', ['user' => null, 'isEdit' => false])
    </form>
@endsection
