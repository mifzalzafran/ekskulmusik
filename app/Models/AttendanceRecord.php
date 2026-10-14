<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceRecordFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'attendance_session_id',
        'user_id',
        'status',
        'notes',
        'recorded_at',
        'recorded_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => AttendanceStatus::class,
            'recorded_at' => 'datetime',
        ];
    }

    /**
     * The session this record belongs to.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    /**
     * The student / user of this attendance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The admin user who recorded this attendance.
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Scope query to a specific status.
     */
    public function scopeStatus(Builder $query, AttendanceStatus|string $status): Builder
    {
        $val = $status instanceof AttendanceStatus ? $status->value : $status;

        return $query->where('status', $val);
    }

    /**
     * Scope query to attended statuses (present or late).
     */
    public function scopeAttended(Builder $query): Builder
    {
        return $query->whereIn('status', [
            AttendanceStatus::Present->value,
            AttendanceStatus::Late->value,
        ]);
    }
}
