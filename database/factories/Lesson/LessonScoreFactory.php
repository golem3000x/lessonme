<?php

declare(strict_types=1);

namespace Database\Factories\Lesson;

use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAttempt;
use App\Models\Lesson\LessonScore;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LessonScore>
 */
class LessonScoreFactory extends Factory
{
    protected $model = LessonScore::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $total_questions = fake()->numberBetween(5, 20);
        $correct_answers = fake()->numberBetween(0, $total_questions);
        $score_percentage = ($total_questions > 0) ? ($correct_answers / $total_questions) * 100 : 0;

        return [
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'attempt_id' => LessonAttempt::factory(),
            'total_questions' => $total_questions,
            'correct_answers' => $correct_answers,
            'score_percentage' => round($score_percentage, 2),
            'graded_at' => now()->subHours(fake()->numberBetween(1, 72)),
        ];
    }

    /**
     * Indicate that the score is excellent (>= 90%).
     */
    public function excellent(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_questions' => 10,
            'correct_answers' => fake()->numberBetween(9, 10),
            'score_percentage' => fake()->randomFloat(2, 90, 100),
        ]);
    }

    /**
     * Indicate that the score is good (>= 75%, < 90%).
     */
    public function good(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_questions' => 10,
            'correct_answers' => fake()->numberBetween(7, 8),
            'score_percentage' => fake()->randomFloat(2, 75, 89.99),
        ]);
    }

    /**
     * Indicate that the score is passing (>= 60%, < 75%).
     */
    public function passing(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_questions' => 10,
            'correct_answers' => fake()->numberBetween(6, 7),
            'score_percentage' => fake()->randomFloat(2, 60, 74.99),
        ]);
    }

    /**
     * Indicate that the score is failing (< 60%).
     */
    public function failing(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_questions' => 10,
            'correct_answers' => fake()->numberBetween(0, 5),
            'score_percentage' => fake()->randomFloat(2, 0, 59.99),
        ]);
    }
}
