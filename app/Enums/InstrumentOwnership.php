<?php

namespace App\Enums;

enum InstrumentOwnership: string
{
    case School = 'school';
    case Student = 'student';

    /**
     * Get the human-readable label for the ownership type.
     */
    public function label(): string
    {
        return match ($this) {
            self::School => 'Milik Eskul',
            self::Student => 'Titipan Siswa',
        };
    }

    /**
     * Badge CSS classes for Tailwind styling.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::School => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
            self::Student => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
        };
    }
}
