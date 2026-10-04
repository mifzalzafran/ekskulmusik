<?php

namespace App\Enums;

enum AttendanceSessionType: string
{
    case Forum = 'forum';
    case Latihan = 'latihan';
    case Upacara = 'upacara';

    /**
     * Get the human-readable label for the session type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Forum => 'Forum',
            self::Latihan => 'Latihan',
            self::Upacara => 'Upacara',
        };
    }
}
