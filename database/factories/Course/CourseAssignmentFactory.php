<?php

declare(strict_types=1);

namespace Database\Factories\Course;

use App\Enums\System\AssignmentStatusEnum;
use App\Models\Course\Course;
use App\Models\Course\CourseAssignment;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseAssignment>
 */
class CourseAssignmentFactory extends Factory
{
    protected $model = CourseAssignment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'teacher_id' => User::factory(),
            'student_id' => User::factory(),
            'assigned_at' => now(),
            'due_date' => null,
            'status' => AssignmentStatusEnum::PENDING,
        ];
    }
}
