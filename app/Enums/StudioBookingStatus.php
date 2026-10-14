<?php

namespace App\Enums;

enum StudioBookingStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    /**
     * Get the human-readable label for the booking status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Konfirmasi',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
            self::Cancelled => 'Dibatalkan',
            self::Completed => 'Selesai',
        };
    }

    /**
     * Badge CSS classes for Tailwind styling.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            self::Approved => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            self::Rejected => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
            self::Cancelled => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400',
            self::Completed => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300',
        };
    }
}
