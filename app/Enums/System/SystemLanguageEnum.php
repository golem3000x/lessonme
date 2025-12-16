<?php

declare(strict_types=1);

namespace App\Enums\System;

use Illuminate\Support\Facades\Lang;

enum SystemLanguageEnum: string
{
    case VIETNAMESE = 'vi';
    case ENGLISH = 'en';
    case THAI = 'th';
    case KHMER = 'km';
    case MALAY = 'ms';
    case BURMESE = 'my';
    case LAO = 'lo';

    public function key(): string
    {
        return match ($this) {
            self::VIETNAMESE => 'vi',
            self::ENGLISH => 'en',
            self::THAI => 'th',
            self::KHMER => 'km',
            self::MALAY => 'ms',
            self::BURMESE => 'my',
            self::LAO => 'lo',
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
            self::THAI => '🇹🇭',
            self::KHMER => '🇰🇭',
            self::MALAY => '🇲🇾',
            self::BURMESE => '🇲🇲',
            self::LAO => '🇱🇦',
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
            self::THAI,
            self::KHMER,
            self::MALAY,
            self::BURMESE,
            self::LAO,
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
            self::THAI->name => self::THAI->value,
            self::KHMER->name => self::KHMER->value,
            self::MALAY->name => self::MALAY->value,
            self::BURMESE->name => self::BURMESE->value,
            self::LAO->name => self::LAO->value,
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
            self::THAI->value,
            self::KHMER->value,
            self::MALAY->value,
            self::BURMESE->value,
            self::LAO->value,
        ];
    }

    public static function flagByValue(string $value): string
    {
        return match ($value) {
            'vi' => self::VIETNAMESE->flag(),
            'en' => self::ENGLISH->flag(),
            'th' => self::THAI->flag(),
            'km' => self::KHMER->flag(),
            'ms' => self::MALAY->flag(),
            'my' => self::BURMESE->flag(),
            'lo' => self::LAO->flag(),
            default => self::ENGLISH->flag(),
        };
    }

    /**
     * Languages available for learning/teaching (lessons available)
     *
     * @return array<int, self>
     */
    public static function learnable(): array
    {
        return [
            self::VIETNAMESE,
            self::ENGLISH,
        ];
    }
}
