<!-- TODO(UI) -->
@extends('layouts.app', ['title' => 'Change Password'])

@section('content')
    <h1>Change Password</h1>

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>

        <div>
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div>
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>

        <div>
            <button type="submit">Update Password</button>
        </div>
    </form>
@endsection
