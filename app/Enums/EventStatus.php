<?php

namespace App\Enums;

enum EventStatus: string
{
    case Upcoming = 'upcoming';
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * Get the human-readable label for the event status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Upcoming => 'Mendatang',
            self::Ongoing => 'Sedang Berlangsung',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /**
     * Badge CSS classes for Tailwind styling.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Upcoming => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300',
            self::Ongoing => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            self::Completed => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400',
            self::Cancelled => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
        };
    }
}
