<?php

declare(strict_types=1);

namespace Database\Factories\Topic;

use App\Models\Topic\Topic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Topic>
 */
class TopicFactory extends Factory
{
    protected $model = Topic::class;

    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug,
            'is_system' => true,
        ];
    }
}
