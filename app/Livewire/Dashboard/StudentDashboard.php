<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAttempt;
use App\Models\Lesson\LessonScore;
use App\Services\GreetingService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StudentDashboard extends Component
{
    public string $greeting = '';

    public int $total_lessons = 0;

    public int $attempted_lessons = 0;

    public int $completed_lessons = 0;

    public float $average_score = 0.0;

    public int $total_attempts = 0;

    public ?float $best_score = null;

    public ?float $latest_score = null;

    /** @var array<int, array{lesson_name: string, score_percentage: float, graded_at: string, grade: string}> */
    public array $recent_scores = [];

    /** @var array<string, int> */
    public array $level_stats = [];

    public function mount(): void
    {
        $user = Auth::user();
        
        if (! $user) {
            return;
        }

        // Get time-based greeting
        $greeting_service = app(GreetingService::class);
        $this->greeting = $greeting_service->getGreeting(
            $user->timezone ?? 'UTC',
            app()->getLocale()
        );

        // Total available lessons
        $this->total_lessons = Lesson::count();

        // Lessons with at least one attempt
        $this->attempted_lessons = LessonAttempt::query()
            ->where('user_id', '=', $user->id)
            ->distinct('lesson_id')
            ->count('lesson_id');

        // Completed lessons (with at least one completed attempt)
        $this->completed_lessons = LessonAttempt::query()
            ->where('user_id', '=', $user->id)
            ->where('is_completed', '=', true)
            ->distinct('lesson_id')
            ->count('lesson_id');

        // Total attempts
        $this->total_attempts = LessonAttempt::query()
            ->where('user_id', '=', $user->id)
            ->where('is_completed', '=', true)
            ->count();

        // Average score
        $avg = LessonScore::query()
            ->where('user_id', '=', $user->id)
            ->avg('score_percentage');
        $this->average_score = $avg !== null ? round((float) $avg, 1) : 0.0;

        // Best score
        $best = LessonScore::query()
            ->where('user_id', '=', $user->id)
            ->max('score_percentage');
        $this->best_score = $best !== null ? round((float) $best, 1) : null;

        // Latest score
        $latest = LessonScore::query()
            ->where('user_id', '=', $user->id)
            ->orderBy('graded_at', 'desc')
            ->first();
        $this->latest_score = $latest !== null ? round($latest->score_percentage, 1) : null;

        // Recent scores (last 5)
        $recent_scores_query = LessonScore::query()
            ->where('user_id', '=', $user->id)
            ->with('lesson')
            ->orderBy('graded_at', 'desc')
            ->limit(5)
            ->get();

        $this->recent_scores = $recent_scores_query->map(function ($score) {
            return [
                'lesson_name' => $score->lesson->lesson_name,
                'score_percentage' => round($score->score_percentage, 1),
                'graded_at' => $score->graded_at->format('Y-m-d H:i'),
                'grade' => $score->getGradeLabel(),
            ];
        })->toArray();

        // Level statistics
        $level_stats = DB::table('lesson_attempts')
            ->join('lessons', 'lesson_attempts.lesson_id', '=', 'lessons.id')
            ->where('lesson_attempts.user_id', '=', $user->id)
            ->where('lesson_attempts.is_completed', '=', true)
            ->select('lessons.language_level', DB::raw('count(*) as count'))
            ->groupBy('lessons.language_level')
            ->get();

        $this->level_stats = $level_stats->pluck('count', 'language_level')->toArray();
    }

    public function render(): Factory|View
    {
        return view('livewire.dashboard.student-dashboard');
    }
}
