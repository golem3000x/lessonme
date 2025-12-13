<?php

declare(strict_types=1);

namespace Database\Factories\Course;

use App\Enums\System\LanguageLevelEnum;
use App\Enums\System\SystemLanguageEnum;
use App\Models\Course\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'language_level' => fake()->randomElement(LanguageLevelEnum::cases()),
            'lesson_lang' => fake()->randomElement([SystemLanguageEnum::ENGLISH, SystemLanguageEnum::VIETNAMESE]),
            'cover_image' => null,
            'is_published' => false,
            'passing_score' => 70,
        ];
    }

    /**
     * Indicate that the course is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    /**
     * Indicate that the course is unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}
