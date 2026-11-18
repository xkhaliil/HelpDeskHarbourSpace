<?php

namespace App\Enums;

enum TicketPriority: string
{
    case Normal = 'normal';
    case High = 'high';
    case Urgent = 'urgent';

    /**
     * The name shown to people in the views.
     */
    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Normal',
            self::High => 'High',
            self::Urgent => 'Urgent',
        };
    }

    /**
     * The Tailwind classes of the coloured badge for this priority.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Normal => 'bg-slate-100 text-slate-700',
            self::High => 'bg-orange-100 text-orange-800',
            self::Urgent => 'bg-red-600 text-white',
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
