<?php

declare(strict_types=1);

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class EmbeddingCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?array
    {
        if (! $value) {
            return [];
        }

        $trimmed = trim($value, '[]');
        if (empty($trimmed)) {
            return [];
        }

        return array_map('floatval', explode(',', $trimmed));
    }

    public function set($model, string $key, $value, array $attributes): ?string
    {
        if (! is_array($value) || empty($value)) {
            return null;
        }

        return '['.implode(',', $value).']';
    }
}
