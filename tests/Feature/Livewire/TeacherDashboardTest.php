<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\System\UserRoleEnum;
use App\Livewire\Dashboard\TeacherDashboard;
use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAssignment;
use App\Models\Lesson\LessonAttempt;
use App\Models\Lesson\LessonScore;
use App\Models\User\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TeacherDashboardTest extends TestCase
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
            ->test(TeacherDashboard::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.dashboard.teacher-dashboard');
    }

    public function test_mount_sets_greeting(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(TeacherDashboard::class)
            ->assertSet('greeting', function ($greeting) {
                return ! empty($greeting);
            });
    }

    // ===== STATISTICS TESTS =====

    public function test_total_students_count_is_zero_initially(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(TeacherDashboard::class)
            ->assertSet('total_students', 0);
    }

    public function test_total_students_count_reflects_connected_students(): void
    {
        $this->teacher->students()->attach($this->student->id);

        Livewire::actingAs($this->teacher)
            ->test(TeacherDashboard::class)
            ->assertSet('total_students', 1);
    }

    public function test_total_lessons_created_count(): void
    {
        Lesson::factory()->count(3)->create(['user_id' => $this->teacher->id]);

        Livewire::actingAs($this->teacher)
            ->test(TeacherDashboard::class)
            ->assertSet('total_lessons_created', 3);
    }

    public function test_total_lessons_assigned_count(): void
    {
        $this->teacher->students()->attach($this->student->id);
        $lesson = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        LessonAssignment::create([
            'lesson_id' => $lesson->id,
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'assigned_at' => now(),
        ]);

        Livewire::actingAs($this->teacher)
            ->test(TeacherDashboard::class)
            ->assertSet('total_lessons_assigned', 1);
    }

    public function test_average_student_score_with_no_scores(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(TeacherDashboard::class)
            ->assertSet('average_student_score', 0.0);
    }

    public function test_average_student_score_calculation(): void
    {
        $this->teacher->students()->attach($this->student->id);
        $lesson = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        $attempt = LessonAttempt::factory()->create([
            'lesson_id' => $lesson->id,
            'user_id' => $this->student->id,
        ]);

        LessonScore::factory()->create([
            'lesson_id' => $lesson->id,
            'user_id' => $this->student->id,
            'attempt_id' => $attempt->id,
            'score_percentage' => 80.0,
        ]);

        $component = Livewire::actingAs($this->teacher)
            ->test(TeacherDashboard::class);

        $this->assertEquals(80.0, $component->get('average_student_score'));
    }

    // ===== ASSIGNED LESSONS TESTS =====

    public function test_assigned_lessons_is_empty_initially(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(TeacherDashboard::class)
            ->assertSet('assigned_lessons', []);
    }

    public function test_assigned_lessons_loads_recent_assignments(): void
    {
        $this->teacher->students()->attach($this->student->id);
        $lesson = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        LessonAssignment::create([
            'lesson_id' => $lesson->id,
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'assigned_at' => now(),
        ]);

        $component = Livewire::actingAs($this->teacher)
            ->test(TeacherDashboard::class);

        $assigned_lessons = $component->get('assigned_lessons');
        $this->assertCount(1, $assigned_lessons);
        $this->assertEquals($lesson->lesson_name, $assigned_lessons[0]['lesson_name']);
    }

    public function test_assigned_lessons_shows_pending_status_when_not_completed(): void
    {
        $this->teacher->students()->attach($this->student->id);
        $lesson = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        LessonAssignment::create([
            'lesson_id' => $lesson->id,
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'assigned_at' => now(),
        ]);

        $component = Livewire::actingAs($this->teacher)
            ->test(TeacherDashboard::class);

        $assigned_lessons = $component->get('assigned_lessons');
        $this->assertEquals('pending', $assigned_lessons[0]['status']);
        $this->assertNull($assigned_lessons[0]['score']);
    }

    public function test_assigned_lessons_shows_completed_status_with_score(): void
    {
        $this->teacher->students()->attach($this->student->id);
        $lesson = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        LessonAssignment::create([
            'lesson_id' => $lesson->id,
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'assigned_at' => now(),
        ]);

        $attempt = LessonAttempt::factory()->create([
            'lesson_id' => $lesson->id,
            'user_id' => $this->student->id,
        ]);

        LessonScore::factory()->create([
            'lesson_id' => $lesson->id,
            'user_id' => $this->student->id,
            'attempt_id' => $attempt->id,
            'score_percentage' => 85.5,
        ]);

        $component = Livewire::actingAs($this->teacher)
            ->test(TeacherDashboard::class);

        $assigned_lessons = $component->get('assigned_lessons');
        $this->assertEquals('completed', $assigned_lessons[0]['status']);
        $this->assertEquals(85.5, $assigned_lessons[0]['score']);
    }

    // ===== STUDENT PERFORMANCE TESTS =====

    public function test_student_performance_is_empty_with_no_students(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(TeacherDashboard::class)
            ->assertSet('student_performance', []);
    }

    public function test_student_performance_includes_connected_students(): void
    {
        $this->teacher->students()->attach($this->student->id);

        $component = Livewire::actingAs($this->teacher)
            ->test(TeacherDashboard::class);

        $performance = $component->get('student_performance');
        $this->assertCount(1, $performance);
        $this->assertEquals($this->student->id, $performance[0]['id']);
        $this->assertEquals($this->student->full_name, $performance[0]['name']);
    }

    public function test_student_performance_tracks_assigned_count(): void
    {
        $this->teacher->students()->attach($this->student->id);
        $lesson1 = Lesson::factory()->create(['user_id' => $this->teacher->id]);
        $lesson2 = Lesson::factory()->create(['user_id' => $this->teacher->id]);

        LessonAssignment::create([
            'lesson_id' => $lesson1->id,
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'assigned_at' => now(),
        ]);

        LessonAssignment::create([
            'lesson_id' => $lesson2->id,
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'assigned_at' => now(),
        ]);

        $component = Livewire::actingAs($this->teacher)
            ->test(TeacherDashboard::class);

        $performance = $component->get('student_performance');
        $this->assertEquals(2, $performance[0]['assigned']);
    }

    // ===== GREETING TESTS =====

    public function test_hide_greeting_sets_show_greeting_to_false(): void
    {
        Livewire::actingAs($this->teacher)
            ->test(TeacherDashboard::class)
            ->call('hideGreeting')
            ->assertSet('show_greeting', false);
    }

    // ===== NON-TEACHER ACCESS TESTS =====

    public function test_non_teacher_gets_empty_data(): void
    {
        Livewire::actingAs($this->student)
            ->test(TeacherDashboard::class)
            ->assertSet('total_students', 0)
            ->assertSet('total_lessons_created', 0)
            ->assertSet('greeting', '');
    }
}
