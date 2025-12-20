<?php

declare(strict_types=1);

namespace App\Services\Lesson;

use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAssignment;
use App\Models\Lesson\LessonAttempt;
use App\Models\Lesson\LessonScore;
use App\Models\User\User;
use Illuminate\Support\Facades\DB;

readonly class LessonGradingService
{
    /**
     * Start a new lesson attempt for a user.
     */
    public function startAttempt(
        Lesson $lesson,
        User $user,
        ?LessonAssignment $assignment = null
    ): LessonAttempt {
        $attempt_number = LessonAttempt::query()
            ->where('lesson_id', '=', $lesson->id)
            ->where('user_id', '=', $user->id)
            ->count() + 1;

        $exercises_snapshot = $assignment !== null ? $assignment->assigned_version : $lesson->exercises;

        return LessonAttempt::create([
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
            'assignment_id' => $assignment?->id,
            'attempt_number' => $attempt_number,
            'exercises_snapshot' => $exercises_snapshot,
            'started_at' => now(),
            'is_completed' => false,
        ]);
    }

    /**
     * Complete an attempt and calculate score.
     *
     * @param  array<string, array<int, string>>  $user_answers
     * @param  array<string, int>  $scores
     */
    public function completeAttempt(
        LessonAttempt $attempt,
        array $user_answers,
        array $scores,
        int $total_score,
        int $max_score
    ): LessonScore {
        return DB::transaction(function () use ($attempt, $user_answers, $total_score, $max_score) {
            $attempt->update([
                'user_answers' => $user_answers,
                'completed_at' => now(),
                'is_completed' => true,
            ]);

            $score_percentage = $max_score > 0
                ? round(($total_score / $max_score) * 100, 2)
                : 0.0;

            return LessonScore::create([
                'attempt_id' => $attempt->id,
                'lesson_id' => $attempt->lesson_id,
                'user_id' => $attempt->user_id,
                'total_questions' => $max_score,
                'correct_answers' => $total_score,
                'score_percentage' => $score_percentage,
                'auto_graded' => true,
                'graded_at' => now(),
            ]);
        });
    }

    /**
     * Get the current active attempt for a user and lesson.
     */
    public function getActiveAttempt(Lesson $lesson, User $user): ?LessonAttempt
    {
        return LessonAttempt::query()
            ->where('lesson_id', '=', $lesson->id)
            ->where('user_id', '=', $user->id)
            ->where('is_completed', '=', false)
            ->latest()
            ->first();
    }

    /**
     * Get assignment for a lesson and student.
     */
    public function getAssignment(Lesson $lesson, User $student): ?LessonAssignment
    {
        return LessonAssignment::query()
            ->where('lesson_id', '=', $lesson->id)
            ->where('student_id', '=', $student->id)
            ->first();
    }

    /**
     * Assign a lesson to a student.
     */
    public function assignLesson(
        Lesson $lesson,
        User $teacher,
        User $student,
        ?\DateTimeInterface $due_date = null
    ): LessonAssignment {
        return LessonAssignment::create([
            'lesson_id' => $lesson->id,
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'assigned_version' => $lesson->exercises,
            'assigned_at' => now(),
            'due_date' => $due_date,
            'status' => \App\Enums\System\AssignmentStatusEnum::PENDING->value,
        ]);
    }

    /**
     * Get all attempts with scores for a lesson.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, LessonAttempt>
     */
    public function getLessonAttempts(Lesson $lesson): \Illuminate\Database\Eloquent\Collection
    {
        return LessonAttempt::query()
            ->where('lesson_id', '=', $lesson->id)
            ->where('is_completed', '=', true)
            ->with(['user', 'score'])
            ->orderBy('completed_at', 'desc')
            ->get();
    }

    /**
     * Get student's best score for a lesson.
     */
    public function getBestScore(Lesson $lesson, User $student): ?LessonScore
    {
        return LessonScore::query()
            ->where('lesson_id', '=', $lesson->id)
            ->where('user_id', '=', $student->id)
            ->orderBy('score_percentage', 'desc')
            ->first();
    }

    /**
     * Get student's latest score for a lesson.
     */
    public function getLatestScore(Lesson $lesson, User $student): ?LessonScore
    {
        return LessonScore::query()
            ->where('lesson_id', '=', $lesson->id)
            ->where('user_id', '=', $student->id)
            ->orderBy('graded_at', 'desc')
            ->first();
    }

    /**
     * Update assignment status based on completion.
     */
    public function updateAssignmentStatus(LessonAssignment $assignment): void
    {
        $has_completed_attempt = LessonAttempt::query()
            ->where('assignment_id', '=', $assignment->id)
            ->where('is_completed', '=', true)
            ->exists();

        if ($has_completed_attempt) {
            $assignment->update([
                'status' => \App\Enums\System\AssignmentStatusEnum::COMPLETED->value,
            ]);
        } elseif ($assignment->isOverdue()) {
            $assignment->update([
                'status' => \App\Enums\System\AssignmentStatusEnum::OVERDUE->value,
            ]);
        }
    }
}
