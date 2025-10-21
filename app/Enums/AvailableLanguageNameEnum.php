<?php
declare(strict_types=1);

namespace App\Enums;

enum AvailableLanguageNameEnum: string
{
    case en = 'English';
    case vi = 'Vietnamese';

    public static function fromKey(string $key): string
    {
        return constant("self::$key")->value;
    }
}
