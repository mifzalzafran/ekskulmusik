<?php

namespace App\Enums;

enum EventType: string
{
    case DemoEskul = 'demo_eskul';
    case Competition = 'competition';
    case PensiSaf = 'pensi_saf';
    case Concert = 'concert';
    case Other = 'other';

    /**
     * Get the human-readable label for the event type.
     */
    public function label(): string
    {
        return match ($this) {
            self::DemoEskul => 'Demo Ekskul',
            self::Competition => 'Lomba / Festival',
            self::PensiSaf => 'Pentas Seni / SAF',
            self::Concert => 'Konser Internal',
            self::Other => 'Acara Lainnya',
        };
    }

    /**
     * Badge CSS classes for Tailwind styling.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::DemoEskul => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            self::Competition => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
            self::PensiSaf => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
            self::Concert => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
            self::Other => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400',
        };
    }
}
