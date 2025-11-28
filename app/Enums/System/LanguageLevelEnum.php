<?php

declare(strict_types=1);

namespace App\Enums\System;

enum LanguageLevelEnum: string
{
    case A1 = 'A1';
    case A2 = 'A2';
    case B1 = 'B1';
    case B2 = 'B2';
    case C1 = 'C1';
    case C2 = 'C2';

    /**
     * @return array<int, self>
     */
    public static function all(): array
    {
        return [
            self::A1,
            self::A2,
            self::B1,
            self::B2,
            self::C1,
            self::C2,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return [
            self::A1->value,
            self::A2->value,
            self::B1->value,
            self::B2->value,
            self::C1->value,
            self::C2->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::A1 => __('lessons.level_a1'),
            self::A2 => __('lessons.level_a2'),
            self::B1 => __('lessons.level_b1'),
            self::B2 => __('lessons.level_b2'),
            self::C1 => __('lessons.level_c1'),
            self::C2 => __('lessons.level_c2'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::A1 => __('lessons.level_a1_desc'),
            self::A2 => __('lessons.level_a2_desc'),
            self::B1 => __('lessons.level_b1_desc'),
            self::B2 => __('lessons.level_b2_desc'),
            self::C1 => __('lessons.level_c1_desc'),
            self::C2 => __('lessons.level_c2_desc'),
        };
    }
}
