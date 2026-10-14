<?php

namespace App\Enums;

enum CashTransactionType: string
{
    case Income = 'income';
    case Expense = 'expense';

    /**
     * Get the human-readable label for the transaction type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Income => 'Pemasukan',
            self::Expense => 'Pengeluaran',
        };
    }

    /**
     * Badge CSS classes for Tailwind styling.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Income => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            self::Expense => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
        };
    }
}
