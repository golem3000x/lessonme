<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Facades\Lang;

enum SystemLanguageEnum: string
{
    case VIETNAMESE = 'vi';
    case ENGLISH = 'en';

    public function key(): string
    {
        return match ($this) {
            self::VIETNAMESE => 'vi',
            self::ENGLISH => 'en',
        };
    }

    public function label(): string
    {
        return Lang::get('languages.'.$this->key());
    }

    public function flag(): string
    {
        return match ($this) {
            self::VIETNAMESE => '🇻🇳',
            self::ENGLISH => '🇬🇧',
        };
    }

    /**
     * @return array<int, self>
     */
    public static function all(): array
    {
        return [
            self::VIETNAMESE,
            self::ENGLISH,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return [
            self::VIETNAMESE->name => self::VIETNAMESE->value,
            self::ENGLISH->name => self::ENGLISH->value,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return [
            self::VIETNAMESE->value,
            self::ENGLISH->value,
        ];
    }
}
