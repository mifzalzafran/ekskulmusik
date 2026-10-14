<?php

namespace App\Models;

use App\Enums\InstrumentOwnership;
use App\Enums\InstrumentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instrument extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'category',
        'brand_model',
        'serial_number',
        'ownership_type',
        'owner_user_id',
        'status',
        'location_slot',
        'condition_notes',
        'photo_path',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ownership_type' => InstrumentOwnership::class,
            'status' => InstrumentStatus::class,
        ];
    }

    /**
     * The owner if this is a student's deposited instrument.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /**
     * Loan history for this instrument.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(InstrumentLoan::class);
    }

    /**
     * Active ongoing loan if the instrument is currently in use.
     */
    public function currentLoan(): HasOne
    {
        return $this->hasOne(InstrumentLoan::class)
            ->whereNull('returned_at')
            ->latestOfMany('borrowed_at');
    }

    /**
     * Check if the instrument is school property.
     */
    public function isSchoolOwned(): bool
    {
        return $this->ownership_type === InstrumentOwnership::School;
    }

    /**
     * Check if the instrument belongs to a student.
     */
    public function isStudentOwned(): bool
    {
        return $this->ownership_type === InstrumentOwnership::Student;
    }

    /**
     * Check if available to borrow in studio.
     */
    public function isAvailable(): bool
    {
        return $this->status === InstrumentStatus::Available;
    }

    /**
     * Check if currently being used / borrowed.
     */
    public function isInUse(): bool
    {
        return $this->status === InstrumentStatus::InUse;
    }

    /**
     * Check if the owner took it home.
     */
    public function isTakenHome(): bool
    {
        return $this->status === InstrumentStatus::TakenHome;
    }

    /**
     * Determine if a user can take this instrument home.
     * Rule: Must be student owned, the given user must be the owner, and it cannot be currently borrowed by someone else.
     */
    public function canBeTakenHomeBy(User $user): bool
    {
        return $this->isStudentOwned()
            && $this->owner_user_id === $user->id
            && $this->status === InstrumentStatus::Available;
    }

    /**
     * Scope query to available instruments.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', InstrumentStatus::Available->value);
    }

    /**
     * Scope query to school-owned instruments.
     */
    public function scopeSchoolOwned(Builder $query): Builder
    {
        return $query->where('ownership_type', InstrumentOwnership::School->value);
    }

    /**
     * Scope query to student-owned deposited instruments.
     */
    public function scopeStudentOwned(Builder $query): Builder
    {
        return $query->where('ownership_type', InstrumentOwnership::Student->value);
    }
}
