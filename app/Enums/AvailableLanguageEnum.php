<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Facades\Lang;

enum AvailableLanguageEnum: int
{
    case Vietnamese = 1;
    case English = 2;

    public function key(): string
    {
        return match ($this) {
            self::Vietnamese => 'Vietnamese',
            self::English => 'English',
        };
    }

    public function label(): string
    {
        return Lang::get('languages.'.$this->key());
    }

    public static function all(): array
    {
        return [
            self::Vietnamese,
            self::English,
        ];
    }
}
