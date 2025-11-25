<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\System\AssignmentStatusEnum;
use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAssignment;
use App\Models\Lesson\LessonAttempt;
use App\Models\Lesson\LessonScore;
use App\Models\User;
use App\Services\Lesson\LessonGradingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonGradingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LessonGradingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LessonGradingService;
    }

    public function test_start_attempt_creates_new_attempt(): void
    {
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();

        $attempt = $this->service->startAttempt($lesson, $user);

        $this->assertInstanceOf(LessonAttempt::class, $attempt);
        $this->assertDatabaseHas('lesson_attempts', [
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
            'attempt_number' => 1,
            'is_completed' => false,
        ]);
    }

    public function test_start_attempt_increments_attempt_number(): void
    {
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();

        // First attempt
        $this->service->startAttempt($lesson, $user);

        // Second attempt
        $attempt2 = $this->service->startAttempt($lesson, $user);

        $this->assertSame(2, $attempt2->attempt_number);
    }

    public function test_start_attempt_saves_exercises_snapshot(): void
    {
        $exercises = [
            ['type' => 'quiz', 'question' => 'What is 2+2?'],
        ];

        $lesson = Lesson::factory()->create([
            'exercises' => $exercises,
        ]);
        $user = User::factory()->create();

        $attempt = $this->service->startAttempt($lesson, $user);

        $this->assertSame($exercises, $attempt->exercises_snapshot);
    }

    public function test_start_attempt_uses_assignment_version_if_provided(): void
    {
        $lesson = Lesson::factory()->create([
            'exercises' => [['type' => 'current']],
        ]);
        $user = User::factory()->create();

        $assignment_exercises = [['type' => 'assigned']];
        $assignment = LessonAssignment::factory()->create([
            'lesson_id' => $lesson->id,
            'student_id' => $user->id,
            'assigned_version' => $assignment_exercises,
        ]);

        $attempt = $this->service->startAttempt($lesson, $user, $assignment);

        $this->assertSame($assignment_exercises, $attempt->exercises_snapshot);
        $this->assertSame($assignment->id, $attempt->assignment_id);
    }

    public function test_complete_attempt_marks_as_completed(): void
    {
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();
        $attempt = $this->service->startAttempt($lesson, $user);

        $user_answers = ['quiz' => ['answer1']];
        $scores = ['quiz' => 1];

        $this->service->completeAttempt($attempt, $user_answers, $scores, 1, 1);

        $this->assertTrue($attempt->fresh()->is_completed);
        $this->assertNotNull($attempt->fresh()->completed_at);
    }

    public function test_complete_attempt_creates_score_record(): void
    {
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();
        $attempt = $this->service->startAttempt($lesson, $user);

        $score = $this->service->completeAttempt(
            $attempt,
            ['quiz' => ['answer']],
            ['quiz' => 8],
            8,
            10
        );

        $this->assertInstanceOf(LessonScore::class, $score);
        $this->assertDatabaseHas('lesson_scores', [
            'attempt_id' => $attempt->id,
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
            'total_questions' => 10,
            'correct_answers' => 8,
            'score_percentage' => 80.0,
        ]);
    }

    public function test_complete_attempt_calculates_percentage_correctly(): void
    {
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();
        $attempt = $this->service->startAttempt($lesson, $user);

        $score = $this->service->completeAttempt(
            $attempt,
            [],
            [],
            7,
            10
        );

        $this->assertSame(70.0, $score->score_percentage);
    }

    public function test_complete_attempt_handles_zero_max_score(): void
    {
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();
        $attempt = $this->service->startAttempt($lesson, $user);

        $score = $this->service->completeAttempt(
            $attempt,
            [],
            [],
            0,
            0
        );

        $this->assertSame(0.0, $score->score_percentage);
    }

    public function test_get_active_attempt_returns_incomplete_attempt(): void
    {
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();

        $active_attempt = $this->service->startAttempt($lesson, $user);

        $result = $this->service->getActiveAttempt($lesson, $user);

        $this->assertNotNull($result);
        $this->assertSame($active_attempt->id, $result->id);
    }

    public function test_get_active_attempt_returns_null_when_all_completed(): void
    {
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();

        $attempt = $this->service->startAttempt($lesson, $user);
        $this->service->completeAttempt($attempt, [], [], 0, 1);

        $result = $this->service->getActiveAttempt($lesson, $user);

        $this->assertNull($result);
    }

    public function test_get_assignment_returns_assignment_for_student(): void
    {
        $lesson = Lesson::factory()->create();
        $student = User::factory()->create();
        $teacher = User::factory()->create();

        $assignment = LessonAssignment::factory()->create([
            'lesson_id' => $lesson->id,
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
        ]);

        $result = $this->service->getAssignment($lesson, $student);

        $this->assertNotNull($result);
        $this->assertSame($assignment->id, $result->id);
    }

    public function test_assign_lesson_creates_assignment(): void
    {
        $lesson = Lesson::factory()->create([
            'exercises' => [['type' => 'quiz']],
        ]);
        $teacher = User::factory()->create();
        $student = User::factory()->create();

        $assignment = $this->service->assignLesson($lesson, $teacher, $student);

        $this->assertInstanceOf(LessonAssignment::class, $assignment);
        $this->assertDatabaseHas('lesson_assignments', [
            'lesson_id' => $lesson->id,
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'status' => AssignmentStatusEnum::PENDING->value,
        ]);
    }

    public function test_assign_lesson_saves_assigned_version(): void
    {
        $exercises = [['type' => 'quiz', 'question' => 'Test?']];
        $lesson = Lesson::factory()->create(['exercises' => $exercises]);
        $teacher = User::factory()->create();
        $student = User::factory()->create();

        $assignment = $this->service->assignLesson($lesson, $teacher, $student);

        $this->assertSame($exercises, $assignment->assigned_version);
    }

    public function test_assign_lesson_with_due_date(): void
    {
        $lesson = Lesson::factory()->create();
        $teacher = User::factory()->create();
        $student = User::factory()->create();
        $due_date = now()->addDays(7);

        $assignment = $this->service->assignLesson($lesson, $teacher, $student, $due_date);

        $this->assertNotNull($assignment->due_date);
        $this->assertSame(
            $due_date->format('Y-m-d H:i:s'),
            $assignment->due_date->format('Y-m-d H:i:s')
        );
    }

    public function test_get_lesson_attempts_returns_completed_attempts(): void
    {
        $lesson = Lesson::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $attempt1 = $this->service->startAttempt($lesson, $user1);
        $this->service->completeAttempt($attempt1, [], [], 5, 10);

        $attempt2 = $this->service->startAttempt($lesson, $user2);
        $this->service->completeAttempt($attempt2, [], [], 8, 10);

        // Create incomplete attempt (should not be included)
        $this->service->startAttempt($lesson, $user1);

        $attempts = $this->service->getLessonAttempts($lesson);

        $this->assertCount(2, $attempts);
    }

    public function test_get_best_score_returns_highest_percentage(): void
    {
        $lesson = Lesson::factory()->create();
        $student = User::factory()->create();

        // First attempt: 50%
        $attempt1 = $this->service->startAttempt($lesson, $student);
        $this->service->completeAttempt($attempt1, [], [], 5, 10);

        // Second attempt: 80%
        $attempt2 = $this->service->startAttempt($lesson, $student);
        $this->service->completeAttempt($attempt2, [], [], 8, 10);

        // Third attempt: 60%
        $attempt3 = $this->service->startAttempt($lesson, $student);
        $this->service->completeAttempt($attempt3, [], [], 6, 10);

        $best_score = $this->service->getBestScore($lesson, $student);

        $this->assertNotNull($best_score);
        $this->assertSame(80.0, $best_score->score_percentage);
    }

    public function test_get_latest_score_returns_most_recent(): void
    {
        $lesson = Lesson::factory()->create();
        $student = User::factory()->create();

        $attempt1 = $this->service->startAttempt($lesson, $student);
        $score1 = $this->service->completeAttempt($attempt1, [], [], 5, 10);

        sleep(1); // Ensure different timestamps

        $attempt2 = $this->service->startAttempt($lesson, $student);
        $score2 = $this->service->completeAttempt($attempt2, [], [], 8, 10);

        $latest_score = $this->service->getLatestScore($lesson, $student);

        $this->assertNotNull($latest_score);
        $this->assertSame($score2->id, $latest_score->id);
    }

    public function test_update_assignment_status_marks_as_completed(): void
    {
        $lesson = Lesson::factory()->create();
        $student = User::factory()->create();
        $teacher = User::factory()->create();

        $assignment = $this->service->assignLesson($lesson, $teacher, $student);

        $attempt = $this->service->startAttempt($lesson, $student, $assignment);
        $this->service->completeAttempt($attempt, [], [], 10, 10);

        $this->service->updateAssignmentStatus($assignment);

        $this->assertSame(
            AssignmentStatusEnum::COMPLETED,
            $assignment->fresh()->status
        );
    }

    public function test_update_assignment_status_marks_as_overdue(): void
    {
        $lesson = Lesson::factory()->create();
        $student = User::factory()->create();
        $teacher = User::factory()->create();

        $assignment = LessonAssignment::factory()->create([
            'lesson_id' => $lesson->id,
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'due_date' => now()->subDay(),
            'status' => AssignmentStatusEnum::PENDING->value,
        ]);

        $this->service->updateAssignmentStatus($assignment);

        $this->assertSame(
            AssignmentStatusEnum::OVERDUE,
            $assignment->fresh()->status
        );
    }
}
