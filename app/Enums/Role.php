<?php

namespace App\Enums;

enum Role: string
{
    case Student = 'student';
    case Teacher = 'teacher';
    case Admin = 'admin';

    /**
     * The name shown to people in the views.
     */
    public function label(): string
    {
        return match ($this) {
            self::Student => 'Student',
            self::Teacher => 'Teacher',
            self::Admin => 'Admin',
        };
    }

    /**
     * The Tailwind classes of the coloured badge for this role.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Student => 'bg-gray-100 text-gray-800',
            self::Teacher => 'bg-harbour-light text-harbour',
            self::Admin => 'bg-harbour text-white',
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
