<?php

declare(strict_types=1);

namespace Database\Factories\Lesson;

use App\Enums\System\AssignmentStatusEnum;
use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAssignment;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LessonAssignment>
 */
class LessonAssignmentFactory extends Factory
{
    protected $model = LessonAssignment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'teacher_id' => User::factory(),
            'student_id' => User::factory(),
            'assigned_version' => [],
            'assigned_at' => now(),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'status' => AssignmentStatusEnum::PENDING->value,
        ];
    }

    /**
     * Indicate that the assignment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssignmentStatusEnum::COMPLETED->value,
        ]);
    }

    /**
     * Indicate that the assignment is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssignmentStatusEnum::OVERDUE->value,
            'due_date' => now()->subDays(1),
        ]);
    }

    /**
     * Set a specific due date.
     */
    public function dueAt(\DateTimeInterface $date): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => $date,
        ]);
    }
}
