<?php

namespace App\Models;

use App\Enums\CashTransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'category',
        'amount',
        'transaction_date',
        'description',
        'user_id',
        'receipt_path',
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
            'type' => CashTransactionType::class,
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    /**
     * The member related to this transaction (e.g. member who paid dues).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The administrator or treasurer who recorded this entry.
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Scope query to income transactions.
     */
    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', CashTransactionType::Income->value);
    }

    /**
     * Scope query to expense transactions.
     */
    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('type', CashTransactionType::Expense->value);
    }

    /**
     * Calculate current net cash balance.
     */
    public static function currentBalance(): float
    {
        $income = (float) static::query()->income()->sum('amount');
        $expense = (float) static::query()->expense()->sum('amount');

        return $income - $expense;
    }
}
