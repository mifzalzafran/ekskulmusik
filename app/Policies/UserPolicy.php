<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $currentUser): Response
    {
        return $currentUser->isAdmin()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses ke data pengguna.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $currentUser, User $model): Response
    {
        return $currentUser->isAdmin()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses untuk melihat pengguna ini.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $currentUser): Response
    {
        return $currentUser->isAdmin()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses untuk menambahkan pengguna.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $currentUser, User $model, ?string $newRole = null): Response
    {
        if (! $currentUser->isAdmin()) {
            return Response::deny('Anda tidak memiliki akses untuk mengubah pengguna ini.');
        }

        // If a new role is supplied and demoting from admin to member
        if ($newRole !== null && $model->isAdmin() && $newRole !== UserRole::Admin->value) {
            return $this->demote($currentUser, $model);
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can demote an admin to a member.
     */
    public function demote(User $currentUser, User $model): Response
    {
        if (! $currentUser->isAdmin()) {
            return Response::deny('Anda tidak memiliki akses untuk mengubah peran pengguna.');
        }

        if ($currentUser->id === $model->id) {
            return Response::deny('Anda tidak dapat menurunkan peran (demote) akun Anda sendiri.');
        }

        if ($model->isAdmin() && $model->is_active && $this->isLastActiveAdmin($model)) {
            return Response::deny('Tidak dapat menurunkan peran admin terakhir yang aktif.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $currentUser, User $model): Response
    {
        if (! $currentUser->isAdmin()) {
            return Response::deny('Anda tidak memiliki akses untuk menghapus pengguna.');
        }

        if ($currentUser->id === $model->id) {
            return Response::deny('Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($model->isAdmin() && $model->is_active && $this->isLastActiveAdmin($model)) {
            return Response::deny('Tidak dapat menghapus admin terakhir yang aktif.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can deactivate the model.
     */
    public function deactivate(User $currentUser, User $model): Response
    {
        if (! $currentUser->isAdmin()) {
            return Response::deny('Anda tidak memiliki akses untuk menonaktifkan pengguna.');
        }

        if ($currentUser->id === $model->id) {
            return Response::deny('Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        if ($model->isAdmin() && $model->is_active && $this->isLastActiveAdmin($model)) {
            return Response::deny('Tidak dapat menonaktifkan admin terakhir yang aktif.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can toggle the active status of the model.
     */
    public function toggleActive(User $currentUser, User $model): Response
    {
        if ($model->is_active) {
            return $this->deactivate($currentUser, $model);
        }

        return $currentUser->isAdmin()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses untuk mengaktifkan pengguna.');
    }

    /**
     * Determine whether the user can toggle choir status.
     */
    public function toggleChoir(User $currentUser, User $model): Response
    {
        return $currentUser->isAdmin()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses untuk mengubah status paduan suara.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $currentUser, User $model): Response
    {
        return $currentUser->isAdmin()
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses untuk memulihkan pengguna.');
    }

    /**
     * Check if the target user is the last remaining active, non-deleted admin.
     */
    protected function isLastActiveAdmin(User $target): bool
    {
        return User::query()
            ->admins()
            ->active()
            ->where('id', '!=', $target->id)
            ->count() === 0;
    }
}
