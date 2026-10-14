<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * Note: 'role' is intentionally excluded to prevent privilege escalation
     * from member-facing inputs; assign it explicitly in admin code.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'nis',
        'class_name',
        'phone',
        'is_choir',
        'is_active',
        'joined_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_choir' => 'boolean',
            'is_active' => 'boolean',
            'joined_at' => 'date',
        ];
    }

    /**
     * Music roles assigned to the user.
     */
    public function musicRoles(): BelongsToMany
    {
        return $this->belongsToMany(MusicRole::class)
            ->withPivot('is_primary', 'skill_level')
            ->withTimestamps();
    }

    /**
     * Attendance records for this user.
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    /**
     * Determine if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * Determine if the user is a member.
     */
    public function isMember(): bool
    {
        return $this->role === UserRole::Member;
    }

    /**
     * Get the primary music role assigned to this user.
     */
    public function primaryMusicRole(): ?MusicRole
    {
        if ($this->relationLoaded('musicRoles')) {
            return $this->musicRoles->firstWhere('pivot.is_primary', true);
        }

        return $this->musicRoles()->wherePivot('is_primary', true)->first();
    }

    /**
     * Scope query to only include admin users.
     */
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', UserRole::Admin->value);
    }

    /**
     * Scope query to only include member users.
     */
    public function scopeMembers(Builder $query): Builder
    {
        return $query->where('role', UserRole::Member->value);
    }

    /**
     * Scope query to only include active users.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query to only include choir members.
     */
    public function scopeChoir(Builder $query): Builder
    {
        return $query->where('is_choir', true);
    }

    /**
     * Scope query to users with a specific music role slug.
     */
    public function scopeWithMusicRole(Builder $query, string $slug): Builder
    {
        return $query->whereHas('musicRoles', function (Builder $q) use ($slug) {
            $q->where('slug', $slug);
        });
    }

    /**
     * Scope query to search by name, email, or NIS.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('nis', 'like', "%{$term}%");
        });
    }
}
