<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Present = 'present';
    case Late = 'late';
    case Excused = 'excused';
    case Sick = 'sick';
    case Absent = 'absent';

    /**
     * Get the human-readable label for the attendance status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Present => 'Hadir',
            self::Late => 'Terlambat',
            self::Excused => 'Izin',
            self::Sick => 'Sakit',
            self::Absent => 'Alpa',
        };
    }

    /**
     * Determine if the status is considered physically attended.
     */
    public function isAttended(): bool
    {
        return in_array($this, [self::Present, self::Late], true);
    }
}
