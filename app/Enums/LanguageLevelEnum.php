<?php

declare(strict_types=1);

namespace App\Enums;

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

    public function description(): string
    {
        return match ($this) {
            self::A1 => 'Beginner — can use very simple expressions.',
            self::A2 => 'Elementary — understands simple phrases and sentences.',
            self::B1 => 'Intermediate — can express opinions on familiar topics.',
            self::B2 => 'Upper Intermediate — can discuss a variety of subjects.',
            self::C1 => 'Advanced — can express ideas fluently and spontaneously.',
            self::C2 => 'Proficient — near-native understanding and production.',
        };
    }
}
