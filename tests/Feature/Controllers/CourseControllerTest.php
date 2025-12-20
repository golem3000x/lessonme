<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Enums\System\AssignmentStatusEnum;
use App\Enums\System\UserRoleEnum;
use App\Models\Course\Course;
use App\Models\Course\CourseAssignment;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;

    private User $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->teacher = User::factory()->create();
        $this->teacher->assignRole(UserRoleEnum::TEACHER);

        $this->student = User::factory()->create();
        $this->student->assignRole(UserRoleEnum::STUDENT);
    }

    public function test_show_returns_view(): void
    {
        $course = Course::factory()->published()->create([
            'user_id' => $this->teacher->id,
        ]);

        // Assign student to the course
        CourseAssignment::create([
            'course_id' => $course->id,
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'assigned_at' => now(),
            'status' => AssignmentStatusEnum::PENDING,
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('courses.show', $course));

        $response->assertStatus(200);
        $response->assertViewIs('courses.show');
        $response->assertViewHas('course');
    }

    public function test_create_returns_view(): void
    {
        $response = $this->actingAs($this->teacher)
            ->get(route('courses.create'));

        $response->assertStatus(200);
        $response->assertViewIs('courses.create');
    }

    public function test_edit_returns_view(): void
    {
        $course = Course::factory()->create([
            'user_id' => $this->teacher->id,
        ]);

        $response = $this->actingAs($this->teacher)
            ->get(route('courses.edit', $course));

        $response->assertStatus(200);
        $response->assertViewIs('courses.edit');
        $response->assertViewHas('course');
    }
}
