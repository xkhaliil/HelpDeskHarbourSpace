<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Solved = 'solved';
    case Closed = 'closed';

    /**
     * The name shown to people in the views.
     */
    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::InProgress => 'In progress',
            self::Solved => 'Solved',
            self::Closed => 'Closed',
        };
    }

    /**
     * The Tailwind classes of the coloured badge for this status.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Open => 'bg-sky-100 text-sky-800',
            self::InProgress => 'bg-amber-100 text-amber-900',
            self::Solved => 'bg-emerald-100 text-emerald-800',
            self::Closed => 'bg-gray-200 text-gray-700',
        };
    }

    /**
     * All allowed values, used in the "in:" validation rules.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
