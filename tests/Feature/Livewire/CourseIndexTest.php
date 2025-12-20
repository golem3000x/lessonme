<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\System\AssignmentStatusEnum;
use App\Enums\System\UserRoleEnum;
use App\Livewire\Course\CourseIndex;
use App\Models\Course\Course;
use App\Models\Course\CourseAssignment;
use App\Models\User\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CourseIndexTest extends TestCase
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

    // ===== RENDER TESTS =====

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(CourseIndex::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.course.course-index');
    }

    public function test_teacher_sees_own_courses(): void
    {
        $course = Course::factory()->create(['user_id' => $this->teacher->id]);

        Livewire::actingAs($this->teacher)
            ->test(CourseIndex::class)
            ->assertSee($course->title);
    }

    public function test_teacher_does_not_see_other_teachers_courses(): void
    {
        $other_teacher = User::factory()->create();
        $other_teacher->assignRole(UserRoleEnum::TEACHER);

        $course = Course::factory()->create(['user_id' => $other_teacher->id]);

        Livewire::actingAs($this->teacher)
            ->test(CourseIndex::class)
            ->assertDontSee($course->title);
    }

    public function test_student_sees_only_assigned_courses(): void
    {
        $published = Course::factory()->published()->create(['user_id' => $this->teacher->id]);
        $unpublished = Course::factory()->unpublished()->create(['user_id' => $this->teacher->id]);

        // Assign student to the published course only
        CourseAssignment::create([
            'course_id' => $published->id,
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'assigned_at' => now(),
            'status' => AssignmentStatusEnum::PENDING,
        ]);

        Livewire::actingAs($this->student)
            ->test(CourseIndex::class)
            ->assertSee($published->title)
            ->assertDontSee($unpublished->title);
    }

    public function test_is_teacher_returns_true_for_teacher(): void
    {
        $component = Livewire::actingAs($this->teacher)
            ->test(CourseIndex::class);

        $this->assertTrue($component->instance()->isTeacher());
    }

    public function test_is_teacher_returns_false_for_student(): void
    {
        $component = Livewire::actingAs($this->student)
            ->test(CourseIndex::class);

        $this->assertFalse($component->instance()->isTeacher());
    }

    // ===== DELETE TESTS =====

    public function test_teacher_can_confirm_delete(): void
    {
        $course = Course::factory()->create(['user_id' => $this->teacher->id]);

        Livewire::actingAs($this->teacher)
            ->test(CourseIndex::class)
            ->call('confirmDelete', $course->id)
            ->assertSet('course_to_delete', $course->id)
            ->assertSet('show_delete_confirm', true);
    }

    public function test_student_cannot_confirm_delete(): void
    {
        $course = Course::factory()->published()->create(['user_id' => $this->teacher->id]);

        Livewire::actingAs($this->student)
            ->test(CourseIndex::class)
            ->call('confirmDelete', $course->id)
            ->assertSet('course_to_delete', null)
            ->assertSet('show_delete_confirm', false);
    }

    public function test_cancel_delete_resets_state(): void
    {
        $course = Course::factory()->create(['user_id' => $this->teacher->id]);

        Livewire::actingAs($this->teacher)
            ->test(CourseIndex::class)
            ->call('confirmDelete', $course->id)
            ->call('cancelDelete')
            ->assertSet('course_to_delete', null)
            ->assertSet('show_delete_confirm', false);
    }

    public function test_teacher_can_delete_own_course(): void
    {
        $course = Course::factory()->create(['user_id' => $this->teacher->id]);

        Livewire::actingAs($this->teacher)
            ->test(CourseIndex::class)
            ->call('confirmDelete', $course->id)
            ->call('deleteCourse');

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_teacher_cannot_delete_other_teachers_course(): void
    {
        $other_teacher = User::factory()->create();
        $other_teacher->assignRole(UserRoleEnum::TEACHER);

        $course = Course::factory()->create(['user_id' => $other_teacher->id]);

        Livewire::actingAs($this->teacher)
            ->test(CourseIndex::class)
            ->set('course_to_delete', $course->id)
            ->set('show_delete_confirm', true)
            ->call('deleteCourse');

        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }

    public function test_student_cannot_delete_course(): void
    {
        $course = Course::factory()->published()->create(['user_id' => $this->teacher->id]);

        Livewire::actingAs($this->student)
            ->test(CourseIndex::class)
            ->set('course_to_delete', $course->id)
            ->set('show_delete_confirm', true)
            ->call('deleteCourse');

        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }

    // ===== GET COURSES TESTS =====

    public function test_get_courses_returns_teacher_courses_with_count(): void
    {
        Course::factory()->count(3)->create(['user_id' => $this->teacher->id]);

        $component = Livewire::actingAs($this->teacher)
            ->test(CourseIndex::class);

        $courses = $component->instance()->getCourses();

        $this->assertCount(3, $courses);
    }

    public function test_courses_ordered_by_created_at_desc(): void
    {
        $old_course = Course::factory()->create([
            'user_id' => $this->teacher->id,
            'created_at' => now()->subDays(2),
        ]);
        $new_course = Course::factory()->create([
            'user_id' => $this->teacher->id,
            'created_at' => now(),
        ]);

        $component = Livewire::actingAs($this->teacher)
            ->test(CourseIndex::class);

        $courses = $component->instance()->getCourses();

        $this->assertEquals($new_course->id, $courses->first()->id);
        $this->assertEquals($old_course->id, $courses->last()->id);
    }
}
