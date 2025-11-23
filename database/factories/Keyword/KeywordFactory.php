<?php

declare(strict_types=1);

namespace Database\Factories\Keyword;

use App\Models\Keyword\Keyword;
use App\Models\Topic\Topic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Keyword>
 */
class KeywordFactory extends Factory
{
    protected $model = Keyword::class;

    public function definition(): array
    {
        return [
            'topic_id' => Topic::factory(),
            'slug' => fake()->unique()->slug,
            'is_system' => true,
        ];
    }
}
