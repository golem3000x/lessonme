<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Lang;

trait HasTranslation
{
    /**
     * Возвращает локализованную метку для enum.
     * Ищет в resources/lang/{locale}/{group}.php
     */
    public function label(?string $group = null): string
    {
        $group ??= $this->translationGroup();

        $key = "{$group}.".$this->key();
        $value = Lang::get($key);

        // Если перевода нет, Laravel вернёт сам ключ (например, "languages.en")
        // Тогда просто вернём красивое fallback-значение
        if ($value === $key) {
            return $this->fallbackLabel();
        }

        return $value;
    }

    /**
     * По умолчанию используем имя enum-класса в нижнем регистре как группу переводов.
     * Например: AvailableLanguageEnum → languages
     */
    protected function translationGroup(): string
    {
        $shortName = class_basename(static::class);

        return strtolower(str_replace('Enum', '', $shortName)).'s';
    }

    /**
     * Возврат fallback-названия (например, English)
     * Можно переопределить в конкретном enum при необходимости.
     */
    protected function fallbackLabel(): string
    {
        return ucfirst($this->key());
    }
}
