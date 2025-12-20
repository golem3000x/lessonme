<?php

declare(strict_types=1);

namespace Database\Factories\Lesson;

use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAttempt;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LessonAttempt>
 */
class LessonAttemptFactory extends Factory
{
    protected $model = LessonAttempt::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'is_completed' => fake()->boolean(70), // 70% chance of being completed
            'started_at' => now()->subMinutes(fake()->numberBetween(5, 60)),
            'completed_at' => function (array $attributes) {
                return $attributes['is_completed']
                    ? now()->subMinutes(fake()->numberBetween(1, 30))
                    : null;
            },
        ];
    }

    /**
     * Indicate that the attempt is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
            'completed_at' => now()->subMinutes(fake()->numberBetween(1, 30)),
        ]);
    }

    /**
     * Indicate that the attempt is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => false,
            'completed_at' => null,
        ]);
    }
}
