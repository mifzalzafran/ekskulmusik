<?php

namespace App\Enums;

enum InstrumentStatus: string
{
    case Available = 'available';
    case InUse = 'in_use';
    case TakenHome = 'taken_home';
    case Maintenance = 'maintenance';

    /**
     * Get the human-readable label for the instrument status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Available => 'Tersedia',
            self::InUse => 'Dipinjam / Digunakan',
            self::TakenHome => 'Dibawa Pulang',
            self::Maintenance => 'Dalam Perbaikan',
        };
    }

    /**
     * Badge CSS classes for Tailwind styling.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Available => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            self::InUse => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            self::TakenHome => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400',
            self::Maintenance => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
        };
    }
}
