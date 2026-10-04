<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Users\SaveUser;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\MusicRole;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users with filters, search, and pagination.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $query = User::query()->with('musicRoles');

        // Filter: show-deleted (support show_deleted or show-deleted)
        $showDeleted = $request->input('show_deleted', $request->input('show-deleted'));
        if ($showDeleted === 'only') {
            $query->onlyTrashed();
        } elseif ($request->boolean('show_deleted') || $request->boolean('show-deleted') || $showDeleted === '1' || $showDeleted === 'true') {
            $query->withTrashed();
        }

        // Search: via User search() scope (name, email, nis)
        $searchTerm = $request->input('search', $request->input('q'));
        if (filled($searchTerm)) {
            $query->search($searchTerm);
        }

        // Filter: role
        if ($request->filled('role')) {
            $roleInput = $request->input('role');
            if ($roleInput instanceof UserRole) {
                $query->where('role', $roleInput->value);
            } elseif (UserRole::tryFrom($roleInput)) {
                $query->where('role', $roleInput);
            }
        }

        // Filter: is_choir
        if ($request->has('is_choir') && $request->input('is_choir') !== '' && $request->input('is_choir') !== null) {
            $query->where('is_choir', $request->boolean('is_choir'));
        }

        // Filter: is_active
        if ($request->has('is_active') && $request->input('is_active') !== '' && $request->input('is_active') !== null) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter: music role slug
        $musicRoleSlug = $request->input('music_role')
            ?? $request->input('music_role_slug')
            ?? $request->input('role_slug');

        if (filled($musicRoleSlug)) {
            $query->withMusicRole($musicRoleSlug);
        }

        $users = $query->latest('id')->paginate(15)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'musicRoles' => MusicRole::active()->orderBy('name')->get(),
            'filters' => $request->only([
                'search',
                'q',
                'role',
                'is_choir',
                'is_active',
                'music_role',
                'music_role_slug',
                'role_slug',
                'show_deleted',
                'show-deleted',
            ]),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        Gate::authorize('create', User::class);

        $musicRoles = MusicRole::active()->orderBy('name')->get();

        return view('admin.users.create', compact('musicRoles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request, SaveUser $saveUser): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $user = $saveUser($request->validated());

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        Gate::authorize('view', $user);

        $user->load('musicRoles');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        $user->load('musicRoles');
        $musicRoles = MusicRole::active()->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'musicRoles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user, SaveUser $saveUser): RedirectResponse
    {
        try {
            Gate::authorize('update', $user);

            if ($user->isAdmin() && $request->input('role') === UserRole::Member->value) {
                Gate::authorize('demote', $user);
            }
        } catch (AuthorizationException $e) {
            return back()->with('error', $e->getMessage())->withErrors(['error' => $e->getMessage()]);
        }

        $saveUser($request->validated(), $user);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage (soft delete).
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            Gate::authorize('delete', $user);
        } catch (AuthorizationException $e) {
            return back()->with('error', $e->getMessage())->withErrors(['error' => $e->getMessage()]);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Pengguna berhasil dihapus.');
    }

    /**
     * Toggle the active status of the specified user.
     */
    public function toggleActive(User $user): RedirectResponse
    {
        try {
            Gate::authorize('toggleActive', $user);
        } catch (AuthorizationException $e) {
            return back()->with('error', $e->getMessage())->withErrors(['error' => $e->getMessage()]);
        }

        $user->update(['is_active' => ! $user->is_active]);

        $message = $user->is_active ? 'Pengguna berhasil diaktifkan.' : 'Pengguna berhasil dinonaktifkan.';

        return back()->with('status', $message);
    }

    /**
     * Toggle choir participation of the specified user.
     */
    public function toggleChoir(User $user): RedirectResponse
    {
        Gate::authorize('toggleChoir', $user);

        $user->update(['is_choir' => ! $user->is_choir]);

        $message = $user->is_choir ? 'Status paduan suara diaktifkan.' : 'Status paduan suara dinonaktifkan.';

        return back()->with('status', $message);
    }

    /**
     * Restore the specified soft-deleted user.
     */
    public function restore(User $user): RedirectResponse
    {
        Gate::authorize('restore', $user);

        $user->restore();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Pengguna berhasil dipulihkan.');
    }
}
