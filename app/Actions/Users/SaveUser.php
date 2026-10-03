<?php

namespace App\Actions\Users;

use App\Enums\SkillLevel;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SaveUser
{
    /**
     * Create or update a user and sync their music talents inside a database transaction.
     *
     * @param  array<string, mixed>  $data
     * @param  \App\Models\User|null  $user
     * @return \App\Models\User
     */
    public function execute(array $data, ?User $user = null): User
    {
        return DB::transaction(function () use ($data, $user) {
            $user ??= new User();

            // Set role explicitly (admin-only, intentionally excluded from User $fillable)
            if (isset($data['role'])) {
                $role = $data['role'] instanceof UserRole
                    ? $data['role']
                    : UserRole::from($data['role']);
                $user->role = $role;
            } elseif (! $user->exists) {
                $user->role = UserRole::Member;
            }

            // Fill mass-assignable attributes
            $user->fill([
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
                'nis' => array_key_exists('nis', $data) ? $data['nis'] : $user->nis,
                'class_name' => array_key_exists('class_name', $data) ? $data['class_name'] : $user->class_name,
                'phone' => array_key_exists('phone', $data) ? $data['phone'] : $user->phone,
                'is_choir' => array_key_exists('is_choir', $data) ? (bool) $data['is_choir'] : ($user->is_choir ?? false),
                'is_active' => array_key_exists('is_active', $data) ? (bool) $data['is_active'] : ($user->is_active ?? true),
                'joined_at' => array_key_exists('joined_at', $data) ? $data['joined_at'] : $user->joined_at,
            ]);

            // Hash and set password if provided
            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            $user->save();

            // Sync music roles / talents
            $this->syncTalents($user, $data);

            return $user->load('musicRoles');
        });
    }

    /**
     * Handle invocation as a callable.
     *
     * @param  array<string, mixed>  $data
     * @param  \App\Models\User|null  $user
     * @return \App\Models\User
     */
    public function __invoke(array $data, ?User $user = null): User
    {
        return $this->execute($data, $user);
    }

    /**
     * Sync music role pivot data for the user.
     *
     * @param  \App\Models\User  $user
     * @param  array<string, mixed>  $data
     * @return void
     */
    protected function syncTalents(User $user, array $data): void
    {
        $syncData = [];

        if (isset($data['music_roles']) && is_array($data['music_roles'])) {
            foreach ($data['music_roles'] as $roleData) {
                $roleId = $roleData['id'] ?? null;
                if ($roleId) {
                    $skill = $roleData['skill_level'] ?? null;
                    if ($skill instanceof SkillLevel) {
                        $skill = $skill->value;
                    }
                    $syncData[$roleId] = [
                        'is_primary' => (bool) ($roleData['is_primary'] ?? false),
                        'skill_level' => $skill,
                    ];
                }
            }
            $user->musicRoles()->sync($syncData);
        } elseif (array_key_exists('music_role_ids', $data)) {
            $roleIds = is_array($data['music_role_ids']) ? $data['music_role_ids'] : [];
            $primaryId = $data['primary_music_role_id'] ?? $data['primary_role_id'] ?? null;
            $skillLevels = is_array($data['skill_levels'] ?? null) ? $data['skill_levels'] : [];

            foreach ($roleIds as $roleId) {
                $skill = $skillLevels[$roleId] ?? null;
                if ($skill instanceof SkillLevel) {
                    $skill = $skill->value;
                }
                $syncData[$roleId] = [
                    'is_primary' => filled($primaryId) && ((int) $roleId === (int) $primaryId),
                    'skill_level' => $skill,
                ];
            }
            $user->musicRoles()->sync($syncData);
        }
    }
}
