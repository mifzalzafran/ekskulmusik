<?php

namespace App\Policies;

use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendanceSessionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AttendanceSession $attendanceSession): bool
    {
        return $user->is_active;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AttendanceSession $attendanceSession): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AttendanceSession $attendanceSession): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can record or update attendance list.
     */
    public function recordAttendance(User $user, AttendanceSession $attendanceSession): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can toggle open/close status.
     */
    public function toggleOpen(User $user, AttendanceSession $attendanceSession): bool
    {
        return $user->isAdmin();
    }
}
