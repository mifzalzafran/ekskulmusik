<!-- TODO(UI) -->
@extends('layouts.app', ['title' => 'Manage Users - Admin'])

@section('content')
    <h1>Users Management</h1>

    <p>
        <a href="{{ route('admin.users.create') }}"><strong>+ Create New User</strong></a>
    </p>

    {{-- Filter Form --}}
    <fieldset>
        <legend>Filters & Search</legend>
        <form action="{{ route('admin.users.index') }}" method="GET">
            <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                <div>
                    <label for="search">Search:</label><br>
                    <input type="text"
                           name="search"
                           id="search"
                           placeholder="Name, email, or NIS..."
                           value="{{ $filters['search'] ?? $filters['q'] ?? '' }}">
                </div>

                <div>
                    <label for="role">Role:</label><br>
                    <select name="role" id="role">
                        <option value="">All Roles</option>
                        @foreach (\App\Enums\UserRole::cases() as $roleOption)
                            <option value="{{ $roleOption->value }}" {{ ($filters['role'] ?? '') === $roleOption->value ? 'selected' : '' }}>
                                {{ $roleOption->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="is_choir">Choir:</label><br>
                    <select name="is_choir" id="is_choir">
                        <option value="">All Statuses</option>
                        <option value="1" {{ ($filters['is_choir'] ?? '') === '1' ? 'selected' : '' }}>Choir Only</option>
                        <option value="0" {{ ($filters['is_choir'] ?? '') === '0' ? 'selected' : '' }}>Non-Choir</option>
                    </select>
                </div>

                <div>
                    <label for="is_active">Active:</label><br>
                    <select name="is_active" id="is_active">
                        <option value="">All Statuses</option>
                        <option value="1" {{ ($filters['is_active'] ?? '') === '1' ? 'selected' : '' }}>Active Only</option>
                        <option value="0" {{ ($filters['is_active'] ?? '') === '0' ? 'selected' : '' }}>Inactive Only</option>
                    </select>
                </div>

                <div>
                    <label for="music_role">Music Talent:</label><br>
                    <select name="music_role" id="music_role">
                        <option value="">All Talents</option>
                        @foreach ($musicRoles as $role)
                            <option value="{{ $role->slug }}" {{ ($filters['music_role'] ?? $filters['music_role_slug'] ?? $filters['role_slug'] ?? '') === $role->slug ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <br>
                    <label>
                        <input type="checkbox"
                               name="show_deleted"
                               value="1"
                               {{ !empty($filters['show_deleted']) || !empty($filters['show-deleted']) ? 'checked' : '' }}>
                        Show Deleted
                    </label>
                </div>

                <div>
                    <br>
                    <button type="submit">Filter</button>
                    <a href="{{ route('admin.users.index') }}">Reset</a>
                </div>
            </div>
        </form>
    </fieldset>
    <br>

    {{-- Users Table --}}
    @if ($users->isEmpty())
        <p>No users found matching the selected criteria.</p>
    @else
        <table border="1" cellpadding="6" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Choir</th>
                    <th>Active</th>
                    <th>Talents (Primary Marked)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr style="{{ $user->trashed() ? 'background-color: #ffe6e6;' : '' }}">
                        <td align="center">{{ $user->id }}</td>
                        <td>
                            <strong>{{ $user->name }}</strong>
                            @if ($user->trashed())
                                <small style="color: red;">[DELETED]</small>
                            @endif
                            @if ($user->nis)
                                <br><small>NIS: {{ $user->nis }}</small>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td align="center">{{ $user->role->label() }}</td>
                        <td align="center">{{ $user->is_choir ? 'Yes' : 'No' }}</td>
                        <td align="center">
                            @if ($user->is_active)
                                <span style="color: green;">Active</span>
                            @else
                                <span style="color: gray;">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @forelse ($user->musicRoles as $role)
                                <span>
                                    {{ $role->name }}
                                    @if ($role->pivot->is_primary)
                                        <strong style="color: green;">(Primary)</strong>
                                    @endif
                                    @if ($role->pivot->skill_level)
                                        <small>[{{ ucfirst($role->pivot->skill_level) }}]</small>
                                    @endif
                                </span>{{ ! $loop->last ? ', ' : '' }}
                            @empty
                                <em>None</em>
                            @endforelse
                        </td>
                        <td align="center">
                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center; flex-wrap: wrap;">
                                <a href="{{ route('admin.users.show', $user) }}">Show</a>

                                <a href="{{ route('admin.users.edit', $user) }}">Edit</a>

                                <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Toggle active status">
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.users.toggle-choir', $user) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Toggle choir membership">
                                        {{ $user->is_choir ? 'Leave Choir' : 'Join Choir' }}
                                    </button>
                                </form>

                                @if ($user->trashed())
                                    <form action="{{ route('admin.users.restore', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Restore soft-deleted user">
                                            Restore
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="color: red;" title="Soft delete user">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 15px;">
            {{ $users->links() }}
        </div>
    @endif
@endsection
