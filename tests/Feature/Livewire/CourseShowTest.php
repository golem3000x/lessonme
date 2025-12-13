<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\System\UserRoleEnum;
use App\Livewire\Course\CourseShow;
use App\Models\Course\Course;
use App\Models\Course\CourseProgress;
use App\Models\Lesson\Lesson;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CourseShowTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private User $teacher;

    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->teacher = User::factory()->create();
        $this->teacher->assignRole(UserRoleEnum::TEACHER);

        $this->student = User::factory()->create();
        $this->student->assignRole(UserRoleEnum::STUDENT);

        $this->course = Course::factory()->published()->create([
            'user_id' => $this->teacher->id,
        ]);
    }

    // ===== RENDER TESTS =====

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->student)
            ->test(CourseShow::class, ['course' => $this->course])
            ->assertStatus(200)
            ->assertViewIs('livewire.course.course-show');
    }

    public function test_mount_sets_course(): void
    {
        Livewire::actingAs($this->student)
            ->test(CourseShow::class, ['course' => $this->course])
            ->assertSet('course.id', $this->course->id);
    }

    // ===== PROGRESS TESTS =====

    public function test_empty_course_shows_zero_progress(): void
    {
        Livewire::actingAs($this->student)
            ->test(CourseShow::class, ['course' => $this->course])
            ->assertSet('total_count', 0)
            ->assertSet('completed_count', 0)
            ->assertSet('progress_percentage', 0);
    }

    public function test_course_with_lessons_shows_correct_total(): void
    {
        $lesson1 = Lesson::factory()->create(['user_id' => $this->teacher->id]);
        $lesson2 = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        $this->course->lessons()->attach($lesson1->id, ['order' => 1, 'is_required' => true]);
        $this->course->lessons()->attach($lesson2->id, ['order' => 2, 'is_required' => true]);

        Livewire::actingAs($this->student)
            ->test(CourseShow::class, ['course' => $this->course])
            ->assertSet('total_count', 2)
            ->assertSet('completed_count', 0);
    }

    public function test_completed_lesson_increases_completed_count(): void
    {
        $lesson1 = Lesson::factory()->create(['user_id' => $this->teacher->id]);
        $lesson2 = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        $this->course->lessons()->attach($lesson1->id, ['order' => 1, 'is_required' => true]);
        $this->course->lessons()->attach($lesson2->id, ['order' => 2, 'is_required' => true]);

        CourseProgress::create([
            'course_id' => $this->course->id,
            'user_id' => $this->student->id,
            'lesson_id' => $lesson1->id,
            'is_passed' => true,
            'score' => 85,
        ]);

        Livewire::actingAs($this->student)
            ->test(CourseShow::class, ['course' => $this->course])
            ->assertSet('completed_count', 1)
            ->assertSet('progress_percentage', 50);
    }

    public function test_all_lessons_completed_shows_100_percent(): void
    {
        $lesson1 = Lesson::factory()->create(['user_id' => $this->teacher->id]);
        $lesson2 = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        $this->course->lessons()->attach($lesson1->id, ['order' => 1, 'is_required' => true]);
        $this->course->lessons()->attach($lesson2->id, ['order' => 2, 'is_required' => true]);

        CourseProgress::create([
            'course_id' => $this->course->id,
            'user_id' => $this->student->id,
            'lesson_id' => $lesson1->id,
            'is_passed' => true,
            'score' => 85,
        ]);

        CourseProgress::create([
            'course_id' => $this->course->id,
            'user_id' => $this->student->id,
            'lesson_id' => $lesson2->id,
            'is_passed' => true,
            'score' => 90,
        ]);

        Livewire::actingAs($this->student)
            ->test(CourseShow::class, ['course' => $this->course])
            ->assertSet('completed_count', 2)
            ->assertSet('progress_percentage', 100);
    }

    // ===== LESSON LOCKING TESTS =====

    public function test_first_lesson_is_unlocked(): void
    {
        $lesson1 = Lesson::factory()->create(['user_id' => $this->teacher->id]);
        $lesson2 = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        $this->course->lessons()->attach($lesson1->id, ['order' => 1, 'is_required' => true]);
        $this->course->lessons()->attach($lesson2->id, ['order' => 2, 'is_required' => true]);

        $component = Livewire::actingAs($this->student)
            ->test(CourseShow::class, ['course' => $this->course]);

        $lessons = $component->get('lessons_with_progress');

        $this->assertFalse($lessons[0]['is_locked']);
    }

    public function test_second_lesson_is_locked_when_first_not_passed(): void
    {
        $lesson1 = Lesson::factory()->create(['user_id' => $this->teacher->id]);
        $lesson2 = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        $this->course->lessons()->attach($lesson1->id, ['order' => 1, 'is_required' => true]);
        $this->course->lessons()->attach($lesson2->id, ['order' => 2, 'is_required' => true]);

        $component = Livewire::actingAs($this->student)
            ->test(CourseShow::class, ['course' => $this->course]);

        $lessons = $component->get('lessons_with_progress');

        $this->assertTrue($lessons[1]['is_locked']);
    }

    public function test_second_lesson_is_unlocked_when_first_passed(): void
    {
        $lesson1 = Lesson::factory()->create(['user_id' => $this->teacher->id]);
        $lesson2 = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        $this->course->lessons()->attach($lesson1->id, ['order' => 1, 'is_required' => true]);
        $this->course->lessons()->attach($lesson2->id, ['order' => 2, 'is_required' => true]);

        CourseProgress::create([
            'course_id' => $this->course->id,
            'user_id' => $this->student->id,
            'lesson_id' => $lesson1->id,
            'is_passed' => true,
            'score' => 85,
        ]);

        $component = Livewire::actingAs($this->student)
            ->test(CourseShow::class, ['course' => $this->course]);

        $lessons = $component->get('lessons_with_progress');

        $this->assertFalse($lessons[1]['is_locked']);
    }

    // ===== LESSONS WITH PROGRESS STRUCTURE TESTS =====

    public function test_lessons_with_progress_has_correct_structure(): void
    {
        $lesson = Lesson::factory()->create(['user_id' => $this->teacher->id]);
        $this->course->lessons()->attach($lesson->id, ['order' => 1, 'is_required' => true]);

        $component = Livewire::actingAs($this->student)
            ->test(CourseShow::class, ['course' => $this->course]);

        $lessons = $component->get('lessons_with_progress');

        $this->assertArrayHasKey('id', $lessons[0]);
        $this->assertArrayHasKey('uuid', $lessons[0]);
        $this->assertArrayHasKey('name', $lessons[0]);
        $this->assertArrayHasKey('order', $lessons[0]);
        $this->assertArrayHasKey('is_locked', $lessons[0]);
        $this->assertArrayHasKey('is_passed', $lessons[0]);
        $this->assertArrayHasKey('score', $lessons[0]);
    }

    public function test_lesson_score_is_included_in_progress(): void
    {
        $lesson = Lesson::factory()->create(['user_id' => $this->teacher->id]);
        $this->course->lessons()->attach($lesson->id, ['order' => 1, 'is_required' => true]);

        CourseProgress::create([
            'course_id' => $this->course->id,
            'user_id' => $this->student->id,
            'lesson_id' => $lesson->id,
            'is_passed' => true,
            'score' => 92,
        ]);

        $component = Livewire::actingAs($this->student)
            ->test(CourseShow::class, ['course' => $this->course]);

        $lessons = $component->get('lessons_with_progress');

        $this->assertEquals(92, $lessons[0]['score']);
    }
}
