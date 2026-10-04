<!-- TODO(UI) -->
@extends('layouts.app', ['title' => 'User Details: ' . $user->name])

@section('content')
    <h1>User Details</h1>

    <p>
        <a href="{{ route('admin.users.index') }}">&larr; Back to Users List</a>
        @if (! $user->trashed())
            | <a href="{{ route('admin.users.edit', $user) }}">Edit User</a>
        @endif
    </p>

    <table border="1" cellpadding="6" cellspacing="0">
        <tbody>
            <tr>
                <th>ID</th>
                <td>{{ $user->id }}</td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th>NIS</th>
                <td>{{ $user->nis ?? '-' }}</td>
            </tr>
            <tr>
                <th>Class</th>
                <td>{{ $user->class_name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Phone</th>
                <td>{{ $user->phone ?? '-' }}</td>
            </tr>
            <tr>
                <th>Role</th>
                <td>{{ $user->role->label() }}</td>
            </tr>
            <tr>
                <th>Choir Member</th>
                <td>{{ $user->is_choir ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $user->is_active ? 'Active' : 'Inactive' }}</td>
            </tr>
            <tr>
                <th>Joined Date</th>
                <td>{{ $user->joined_at?->format('Y-m-d') ?? '-' }}</td>
            </tr>
            <tr>
                <th>Email Verified</th>
                <td>{{ $user->email_verified_at?->format('Y-m-d H:i:s') ?? 'Not verified' }}</td>
            </tr>
            <tr>
                <th>Created At</th>
                <td>{{ $user->created_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
            </tr>
            <tr>
                <th>Updated At</th>
                <td>{{ $user->updated_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
            </tr>
            @if ($user->trashed())
                <tr style="color: red;">
                    <th>Deleted At (Soft Deleted)</th>
                    <td>{{ $user->deleted_at?->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <h2>Music Talents / Roles</h2>
    @if ($user->musicRoles->isEmpty())
        <p>No music talents assigned.</p>
    @else
        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>Role Name</th>
                    <th>Slug</th>
                    <th>Primary Talent</th>
                    <th>Skill Level</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user->musicRoles as $role)
                    <tr>
                        <td><strong>{{ $role->name }}</strong></td>
                        <td><code>{{ $role->slug }}</code></td>
                        <td>
                            @if ($role->pivot->is_primary)
                                <strong style="color: green;">Yes (Primary)</strong>
                            @else
                                No
                            @endif
                        </td>
                        <td>{{ $role->pivot->skill_level ? ucfirst($role->pivot->skill_level) : '-' }}</td>
                        <td>{{ $role->description ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Actions</h2>
    <div style="display: flex; gap: 10px; align-items: center;">
        @if ($user->trashed())
            <form action="{{ route('admin.users.restore', $user) }}" method="POST" style="display:inline;">
                @csrf
                @method('PATCH')
                <button type="submit">Restore User</button>
            </form>
        @else
            <a href="{{ route('admin.users.edit', $user) }}">Edit</a>

            @if (auth()->id() !== $user->id)
                <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit">
                        {{ $user->is_active ? 'Deactivate Account' : 'Activate Account' }}
                    </button>
                </form>
            @endif

            <form action="{{ route('admin.users.toggle-choir', $user) }}" method="POST" style="display:inline;">
                @csrf
                @method('PATCH')
                <button type="submit">
                    {{ $user->is_choir ? 'Remove from Choir' : 'Assign to Choir' }}
                </button>
            </form>

            @if (auth()->id() !== $user->id)
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color: red;">Delete User</button>
                </form>
            @endif
        @endif
    </div>
@endsection
