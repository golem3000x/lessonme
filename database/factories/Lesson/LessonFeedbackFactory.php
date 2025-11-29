<?php

declare(strict_types=1);

namespace Database\Factories\Lesson;

use App\Enums\FeedbackRatingEnum;
use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonFeedback;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LessonFeedback>
 */
class LessonFeedbackFactory extends Factory
{
    protected $model = LessonFeedback::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'user_id' => User::factory(),
            'rating' => fake()->randomElement([FeedbackRatingEnum::LIKE, FeedbackRatingEnum::DISLIKE]),
            'comment' => fake()->optional(0.7)->sentence(),
        ];
    }

    /**
     * Indicate that the feedback is positive (like).
     */
    public function like(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => FeedbackRatingEnum::LIKE,
        ]);
    }

    /**
     * Indicate that the feedback is negative (dislike).
     */
    public function dislike(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => FeedbackRatingEnum::DISLIKE,
        ]);
    }

    /**
     * Indicate that the feedback has no comment.
     */
    public function withoutComment(): static
    {
        return $this->state(fn (array $attributes) => [
            'comment' => null,
        ]);
    }

    /**
     * Indicate that the feedback has a long comment.
     */
    public function withLongComment(): static
    {
        return $this->state(fn (array $attributes) => [
            'comment' => fake()->paragraph(3),
        ]);
    }

    /**
     * Indicate that the feedback has a specific comment.
     */
    public function withComment(string $comment): static
    {
        return $this->state(fn (array $attributes) => [
            'comment' => $comment,
        ]);
    }
}
