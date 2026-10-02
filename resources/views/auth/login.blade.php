<!-- TODO(UI) -->
@extends('layouts.app', ['title' => 'Login'])

@section('content')
    <h1>Log In</h1>

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div>
            <label for="remember">
                <input type="checkbox" id="remember" name="remember" value="1">
                Remember Me
            </label>
        </div>

        <div>
            <button type="submit">Log In</button>
        </div>
    </form>
@endsection
