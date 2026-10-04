<!-- TODO(UI) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Music Extracurricular Management' }}</title>
</head>
<body>
    <header>
        <nav>
            @auth
                <span>Logged in as: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role->label() }})</span>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                    <a href="{{ route('admin.users.index') }}">Users</a>
                @elseif (auth()->user()->isMember())
                    <a href="{{ route('member.dashboard') }}">Member Dashboard</a>
                @endif
                <a href="{{ route('password.edit') }}">Change Password</a>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit">Log Out</button>
                </form>
            @else
                <a href="{{ route('login') }}">Log In</a>
            @endauth
        </nav>
    </header>

    <main>
        {{-- Flash Messages --}}
        @if (session('status'))
            <div style="color: green;">
                {{ session('status') }}
            </div>
        @endif

        @if (session('success'))
            <div style="color: green;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div style="color: red;">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
