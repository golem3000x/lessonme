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

    public static function all(): array
    {
        return [
            self::VIETNAMESE,
            self::ENGLISH,
        ];
    }
}
