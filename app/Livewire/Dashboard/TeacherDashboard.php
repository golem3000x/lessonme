<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAssignment;
use App\Models\Lesson\LessonScore;
use App\Models\User\User;
use App\Services\GreetingService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TeacherDashboard extends Component
{
    public string $greeting = '';

    public bool $show_greeting = true;

    public int $total_students = 0;

    public int $total_lessons_created = 0;

    public int $total_lessons_assigned = 0;

    public float $average_student_score = 0.0;

    /** @var array<int, array{id: int, lesson_name: string, student_name: string, assigned_at: string, status: string, score: float|null}> */
    public array $assigned_lessons = [];

    /** @var array<int, array{id: int, name: string, assigned: int, completed: int, avg_score: float, last_active: string|null}> */
    public array $student_performance = [];

    /** @var array<int, array{type: string, message: string, time: string}> */
    public array $recent_activity = [];

    public function mount(): void
    {
        $teacher = Auth::user();

        if (! $teacher instanceof User || ! $teacher->isTeacher()) {
            return;
        }

        // Check if greeting should be shown
        $this->show_greeting = $teacher->shouldShowGreeting();

        // Get time-based greeting
        $greeting_service = app(GreetingService::class);
        $this->greeting = $greeting_service->getGreeting(
            $teacher->timezone ?? 'UTC',
            app()->getLocale()
        );

        // Total connected students
        $this->total_students = $teacher->students()->count();

        // Total lessons created by this teacher
        $this->total_lessons_created = Lesson::where('user_id', '=', $teacher->id)->count();

        // Total lesson assignments
        $this->total_lessons_assigned = LessonAssignment::where('teacher_id', '=', $teacher->id)->count();

        // Average student score across all assigned lessons
        $student_ids = $teacher->students()->pluck('users.id')->toArray();
        if (! empty($student_ids)) {
            $avg = LessonScore::whereIn('user_id', $student_ids)
                ->whereHas('lesson', function (Builder $query) use ($teacher): void {
                    $query->where('user_id', '=', $teacher->id);
                })
                ->avg('score_percentage');
            $this->average_student_score = $avg !== null ? round((float) $avg, 1) : 0.0;
        }

        // Load assigned lessons with status
        $this->loadAssignedLessons($teacher);

        // Load student performance
        $this->loadStudentPerformance($teacher);

        // Load recent activity
        $this->loadRecentActivity($teacher);
    }

    private function loadAssignedLessons(User $teacher): void
    {
        $assignments = LessonAssignment::where('teacher_id', '=', $teacher->id)
            ->with(['lesson', 'student'])
            ->orderBy('assigned_at', 'desc')
            ->limit(10)
            ->get();

        $this->assigned_lessons = $assignments->map(function (LessonAssignment $assignment): array {
            // Check if student completed the lesson
            $score = LessonScore::where('user_id', '=', $assignment->student_id)
                ->where('lesson_id', '=', $assignment->lesson_id)
                ->orderBy('graded_at', 'desc')
                ->first();

            return [
                'id' => $assignment->id,
                'lesson_name' => $assignment->lesson->lesson_name ?? __('dashboard.unknown_lesson'),
                'student_name' => $assignment->student->full_name ?? __('dashboard.unknown_student'),
                'assigned_at' => $assignment->assigned_at->format('M d, Y'),
                'status' => $score !== null ? 'completed' : 'pending',
                'score' => $score?->score_percentage !== null ? round($score->score_percentage, 1) : null,
            ];
        })->toArray();
    }

    private function loadStudentPerformance(User $teacher): void
    {
        $students = $teacher->students()->get();

        $this->student_performance = $students->map(function (User $student) use ($teacher): array {
            // Count assigned lessons
            $assigned = LessonAssignment::where('teacher_id', '=', $teacher->id)
                ->where('student_id', '=', $student->id)
                ->count();

            // Count completed lessons (has score)
            $completed = LessonScore::where('user_id', '=', $student->id)
                ->whereHas('lesson', function (Builder $query) use ($teacher): void {
                    $query->where('user_id', '=', $teacher->id);
                })
                ->distinct('lesson_id')
                ->count('lesson_id');

            // Average score
            $avg = LessonScore::where('user_id', '=', $student->id)
                ->whereHas('lesson', function (Builder $query) use ($teacher): void {
                    $query->where('user_id', '=', $teacher->id);
                })
                ->avg('score_percentage');

            // Last activity
            $last_score = LessonScore::where('user_id', '=', $student->id)
                ->orderBy('graded_at', 'desc')
                ->first();

            return [
                'id' => $student->id,
                'name' => $student->full_name,
                'avatar' => $student->avatar_url,
                'assigned' => $assigned,
                'completed' => $completed,
                'avg_score' => $avg !== null ? round((float) $avg, 1) : 0.0,
                'last_active' => $last_score?->graded_at?->diffForHumans(),
            ];
        })->toArray();
    }

    private function loadRecentActivity(User $teacher): void
    {
        $activities = [];

        // Recent lesson completions by students
        $student_ids = $teacher->students()->pluck('users.id')->toArray();

        if (! empty($student_ids)) {
            $recent_scores = LessonScore::whereIn('user_id', $student_ids)
                ->whereHas('lesson', function (Builder $query) use ($teacher): void {
                    $query->where('user_id', '=', $teacher->id);
                })
                ->with(['user', 'lesson'])
                ->orderBy('graded_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($recent_scores as $score) {
                $activities[] = [
                    'type' => 'completion',
                    'message' => __('dashboard.activity_completed', [
                        'student' => $score->user->full_name,
                        'lesson' => $score->lesson->lesson_name,
                        'score' => round($score->score_percentage, 0),
                    ]),
                    'time' => $score->graded_at->diffForHumans(),
                ];
            }
        }

        // Recent assignments
        $recent_assignments = LessonAssignment::where('teacher_id', '=', $teacher->id)
            ->with(['lesson', 'student'])
            ->orderBy('assigned_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recent_assignments as $assignment) {
            $activities[] = [
                'type' => 'assignment',
                'message' => __('dashboard.activity_assigned', [
                    'lesson' => $assignment->lesson->lesson_name,
                    'student' => $assignment->student->full_name,
                ]),
                'time' => $assignment->assigned_at->diffForHumans(),
            ];
        }

        // Sort by time and take first 10
        usort($activities, function (array $a, array $b): int {
            return strtotime($b['time']) <=> strtotime($a['time']);
        });

        $this->recent_activity = array_slice($activities, 0, 10);
    }

    public function hideGreeting(): void
    {
        $this->show_greeting = false;
    }

    public function render(): Factory|View
    {
        return view('livewire.dashboard.teacher-dashboard');
    }
}
