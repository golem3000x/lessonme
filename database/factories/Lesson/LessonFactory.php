<?php

declare(strict_types=1);

namespace Database\Factories\Lesson;

use App\Enums\System\LanguageLevelEnum;
use App\Models\Lesson\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'topic_id' => 1, // Default topic_id to avoid NOT NULL constraint
            'lesson_name' => fake()->sentence(3),
            'lesson_text' => fake()->paragraphs(3, true),
            'language_level' => fake()->randomElement(LanguageLevelEnum::cases())->value,
            'grammar_data' => [],
            'vocabulary' => [],
            'dialogue' => [],
            'exercises' => [],
            'homework' => '',
            'common_mistakes' => [],
            'culture_notes' => '',
            'embedding' => [],
        ];
    }

    /**
     * Indicate that the lesson has a specific language level.
     */
    public function level(LanguageLevelEnum $level): static
    {
        return $this->state(fn (array $attributes) => [
            'language_level' => $level->value,
        ]);
    }

    /**
     * Indicate that the lesson belongs to a specific topic.
     */
    public function forTopic(int $topic_id): static
    {
        return $this->state(fn (array $attributes) => [
            'topic_id' => $topic_id,
        ]);
    }

    /**
     * Indicate that the lesson has exercises.
     * 
     * @param array<int, array<string, mixed>> $exercises
     */
    public function withExercises(array $exercises = []): static
    {
        return $this->state(fn (array $attributes) => [
            'exercises' => $exercises ?: [
                ['type' => 'quiz', 'question' => 'Test question?', 'options' => ['A', 'B', 'C'], 'correct' => 0],
            ],
        ]);
    }
}
