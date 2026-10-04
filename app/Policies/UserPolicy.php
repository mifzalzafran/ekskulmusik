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

        if ($model->trashed()) {
            return Response::deny('Pengguna yang telah dihapus tidak dapat diubah.');
        }

        // If a new role is supplied and demoting or changing role
        if ($newRole !== null && $newRole !== ($model->role instanceof UserRole ? $model->role->value : $model->role)) {
            return $this->changeRole($currentUser, $model, $newRole);
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can change the role of a user.
     */
    public function changeRole(User $currentUser, User $model, ?string $newRole = null): Response
    {
        if (! $currentUser->isAdmin()) {
            return Response::deny('Anda tidak memiliki akses untuk mengubah peran pengguna.');
        }

        if ($model->trashed()) {
            return Response::deny('Pengguna yang telah dihapus tidak dapat diubah perannya.');
        }

        if ($currentUser->id === $model->id) {
            return Response::deny('Anda tidak dapat mengubah peran akun Anda sendiri.');
        }

        if ($model->isAdmin() && ($this->isLastAdmin($model) || $this->isLastActiveAdmin($model))) {
            return Response::deny('Tidak dapat mengubah peran admin terakhir.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can demote an admin to a member.
     */
    public function demote(User $currentUser, User $model): Response
    {
        return $this->changeRole($currentUser, $model, UserRole::Member->value);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $currentUser, User $model): Response
    {
        if (! $currentUser->isAdmin()) {
            return Response::deny('Anda tidak memiliki akses untuk menghapus pengguna.');
        }

        if ($model->trashed()) {
            return Response::deny('Pengguna sudah dalam status terhapus.');
        }

        if ($currentUser->id === $model->id) {
            return Response::deny('Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($model->isAdmin() && ($this->isLastAdmin($model) || $this->isLastActiveAdmin($model))) {
            return Response::deny('Tidak dapat menghapus admin terakhir.');
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

        if ($model->trashed()) {
            return Response::deny('Pengguna yang telah dihapus tidak dapat dinonaktifkan.');
        }

        if ($currentUser->id === $model->id) {
            return Response::deny('Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        if ($model->isAdmin() && ($this->isLastAdmin($model) || $this->isLastActiveAdmin($model))) {
            return Response::deny('Tidak dapat menonaktifkan admin terakhir.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can toggle the active status of the model.
     */
    public function toggleActive(User $currentUser, User $model): Response
    {
        if ($model->trashed()) {
            return Response::deny('Pengguna yang telah dihapus tidak dapat diubah status keaktifannya.');
        }

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
        if (! $currentUser->isAdmin()) {
            return Response::deny('Anda tidak memiliki akses untuk mengubah status paduan suara.');
        }

        if ($model->trashed()) {
            return Response::deny('Pengguna yang telah dihapus tidak dapat diubah status paduan suaranya.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $currentUser, User $model): Response
    {
        if (! $currentUser->isAdmin()) {
            return Response::deny('Anda tidak memiliki akses untuk memulihkan pengguna.');
        }

        if (! $model->trashed()) {
            return Response::deny('Pengguna tidak dalam status terhapus.');
        }

        return Response::allow();
    }

    /**
     * Check if the target user is the last remaining admin in the system.
     */
    protected function isLastAdmin(User $target): bool
    {
        return User::query()
            ->admins()
            ->where('id', '!=', $target->id)
            ->count() === 0;
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
