<?php

namespace App\Models;

use App\Enums\StudioBookingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudioBooking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'studio_room_id',
        'user_id',
        'booking_date',
        'start_time',
        'end_time',
        'purpose',
        'status',
        'approved_by',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'status' => StudioBookingStatus::class,
        ];
    }

    /**
     * The studio room for this booking.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(StudioRoom::class, 'studio_room_id');
    }

    /**
     * The user / member who made this booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The admin user who approved or rejected this booking.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope query to pending bookings.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', StudioBookingStatus::Pending->value);
    }

    /**
     * Scope query to approved bookings.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', StudioBookingStatus::Approved->value);
    }

    /**
     * Scope query to upcoming bookings from today onwards.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('booking_date', '>=', now()->toDateString())
            ->orderBy('booking_date')
            ->orderBy('start_time');
    }

    /**
     * Check if a time slot has conflicts in a given room on a given date.
     */
    public static function hasConflict(int $roomId, string $date, string $startTime, string $endTime, ?int $ignoreBookingId = null): bool
    {
        return static::query()
            ->where('studio_room_id', $roomId)
            ->where('booking_date', $date)
            ->whereIn('status', [StudioBookingStatus::Approved->value, StudioBookingStatus::Pending->value])
            ->when($ignoreBookingId, fn (Builder $q) => $q->where('id', '!=', $ignoreBookingId))
            ->where(function (Builder $q) use ($startTime, $endTime) {
                $q->where(function ($sub) use ($startTime, $endTime) {
                    $sub->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                });
            })
            ->exists();
    }
}
