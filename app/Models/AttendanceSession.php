<?php

namespace App\Models;

use App\Enums\AttendanceSessionType;
use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSession extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceSessionFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'session_type',
        'date',
        'start_time',
        'end_time',
        'location',
        'description',
        'is_open',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'session_type' => AttendanceSessionType::class,
            'is_open' => 'boolean',
        ];
    }

    /**
     * Attendance records for this session.
     */
    public function records(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    /**
     * The admin user who created this session.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope query to open sessions.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('is_open', true);
    }

    /**
     * Scope query to sessions of a specific type.
     */
    public function scopeOfType(Builder $query, AttendanceSessionType|string $type): Builder
    {
        $val = $type instanceof AttendanceSessionType ? $type->value : $type;

        return $query->where('session_type', $val);
    }

    /**
     * Scope query to upcoming sessions.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('date', '>=', now()->toDateString())->orderBy('date');
    }

    /**
     * Scope query to past sessions.
     */
    public function scopePast(Builder $query): Builder
    {
        return $query->where('date', '<', now()->toDateString())->latest('date');
    }

    /**
     * Get summary counts of attendances grouped by status.
     *
     * @return array<string, int>
     */
    public function countsByStatus(): array
    {
        $records = $this->records;

        return [
            AttendanceStatus::Present->value => $records->where('status', AttendanceStatus::Present)->count(),
            AttendanceStatus::Late->value => $records->where('status', AttendanceStatus::Late)->count(),
            AttendanceStatus::Excused->value => $records->where('status', AttendanceStatus::Excused)->count(),
            AttendanceStatus::Sick->value => $records->where('status', AttendanceStatus::Sick)->count(),
            AttendanceStatus::Absent->value => $records->where('status', AttendanceStatus::Absent)->count(),
        ];
    }
}
