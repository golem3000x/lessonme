<?php

declare(strict_types=1);

namespace App\Livewire\Lesson;

use App\Models\Lesson\Lesson;
use App\Models\Lesson\LessonAttempt;
use App\Services\Lesson\LessonGradingService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LessonExercises extends Component
{
    public ?int $lesson_id = null;

    public ?Lesson $lesson = null;

    public ?int $attempt_id = null;

    public ?string $learning_lang = null;

    /** @var array<string, mixed> */
    public array $exercises = [];

    /** @var array<int, array{type: string, index: int, data: array<string, mixed>}> */
    public array $all_exercises_flat = [];

    public int $current_exercise_index = 0;

    public int $total_exercises = 0;

    /** @var array<string, array<int, string>> */
    public array $user_answers = [
        'gap_fill' => [],
        'sentence_ordering' => [],
        'true_false' => [],
        'matching' => [],
        'quiz' => [],
    ];

    /** @var array<int, array<int, string>> */
    public array $ordered_words = [];

    public bool $checked = false;

    public bool $show_confirm_modal = false;

    /** @var array<string, int> */
    public array $scores = [
        'gap_fill' => 0,
        'sentence_ordering' => 0,
        'true_false' => 0,
        'matching' => 0,
        'quiz' => 0,
    ];

    public int $total_score = 0;

    public int $max_score = 0;

    /**
     * @param  array<string, mixed>  $exercises
     */
    public function mount(array $exercises, ?int $lesson_id = null): void
    {
        $this->exercises = $exercises;
        $this->lesson_id = $lesson_id;

        // Load lesson for audio files access
        if ($lesson_id) {
            $this->lesson = Lesson::with('audioFiles')->find($lesson_id);
            if ($this->lesson && $this->lesson->lesson_lang) {
                $this->learning_lang = $this->lesson->lesson_lang->value;
            }
        }

        // Build flat array of all exercises
        $flat_exercises = [];

        foreach (['quiz', 'gap_fill', 'sentence_ordering', 'true_false', 'matching'] as $type) {
            if (! empty($exercises[$type])) {
                foreach ($exercises[$type] as $index => $data) {
                    $flat_exercises[] = [
                        'type' => $type,
                        'index' => $index,
                        'data' => $data,
                    ];
                }
            }
        }

        $this->all_exercises_flat = $flat_exercises;
        $this->total_exercises = count($flat_exercises);

        // Initialize ordered_words for sentence_ordering exercises
        if (! empty($exercises['sentence_ordering'])) {
            foreach ($exercises['sentence_ordering'] as $index => $item) {
                if (isset($item['words']) && is_array($item['words'])) {
                    $this->ordered_words[$index] = $item['words'];
                }
            }
        }

        // Calculate max score
        $this->max_score = count($exercises['gap_fill'] ?? [])
            + count($exercises['sentence_ordering'] ?? [])
            + count($exercises['true_false'] ?? [])
            + count($exercises['matching'] ?? [])
            + count($exercises['quiz'] ?? []); // NEW;

        // Start attempt if lesson_id is provided
        if ($this->lesson_id !== null && Auth::check()) {
            $this->startAttempt();
        }
    }

    protected function startAttempt(): void
    {
        $lesson = Lesson::find($this->lesson_id);
        if ($lesson === null) {
            return;
        }

        $grading_service = app(LessonGradingService::class);
        $user = Auth::user();

        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        // Check for existing active attempt
        $active_attempt = $grading_service->getActiveAttempt($lesson, $user);

        if ($active_attempt !== null) {
            $this->attempt_id = $active_attempt->id;

            return;
        }

        // Get assignment if exists
        $assignment = $grading_service->getAssignment($lesson, $user);

        // Create new attempt
        $attempt = $grading_service->startAttempt($lesson, $user, $assignment);
        $this->attempt_id = $attempt->id;
    }

    public function moveWord(int $exercise_index, int $from_index, string $direction): void
    {
        if (! isset($this->ordered_words[$exercise_index])) {
            return;
        }

        $words = $this->ordered_words[$exercise_index];
        $to_index = $direction === 'left' ? $from_index - 1 : $from_index + 1;

        if ($to_index < 0 || $to_index >= count($words)) {
            return;
        }

        // Swap words
        $temp = $words[$from_index];
        $words[$from_index] = $words[$to_index];
        $words[$to_index] = $temp;

        $this->ordered_words[$exercise_index] = $words;
    }

    public function nextExercise(): void
    {
        if ($this->current_exercise_index < $this->total_exercises - 1) {
            $this->current_exercise_index++;
        }
    }

    public function previousExercise(): void
    {
        if ($this->current_exercise_index > 0) {
            $this->current_exercise_index--;
        }
    }

    public function showConfirmModal(): void
    {
        $this->show_confirm_modal = true;
    }

    public function closeConfirmModal(): void
    {
        $this->show_confirm_modal = false;
    }

    public function checkAnswers(): void
    {
        $this->show_confirm_modal = false;
        $this->checked = true;
        $this->total_score = 0;

        // Check gap_fill
        if (! empty($this->exercises['gap_fill'])) {
            $this->scores['gap_fill'] = 0;
            foreach ($this->exercises['gap_fill'] as $i => $item) {
                $user_answer = trim($this->user_answers['gap_fill'][$i] ?? '');
                $correct_answer = trim($item['answer'] ?? '');
                if (strcasecmp($user_answer, $correct_answer) === 0) {
                    $this->scores['gap_fill']++;
                }
            }
            $this->total_score += $this->scores['gap_fill'];
        }

        // Check sentence_ordering
        if (! empty($this->exercises['sentence_ordering'])) {
            $this->scores['sentence_ordering'] = 0;
            foreach ($this->exercises['sentence_ordering'] as $i => $item) {
                $user_answer = implode(' ', $this->ordered_words[$i] ?? []);
                $correct_answer = trim($item['answer'] ?? '');

                // Strip punctuation for comparison
                $user_answer_clean = preg_replace('/[^\p{L}\p{N}\s]/u', '', $user_answer);
                $correct_answer_clean = preg_replace('/[^\p{L}\p{N}\s]/u', '', $correct_answer);

                $user_clean = is_string($user_answer_clean) ? trim($user_answer_clean) : '';
                $correct_clean = is_string($correct_answer_clean) ? trim($correct_answer_clean) : '';

                if (strcasecmp($user_clean, $correct_clean) === 0) {
                    $this->scores['sentence_ordering']++;
                }
            }
            $this->total_score += $this->scores['sentence_ordering'];
        }

        // Check true_false
        if (! empty($this->exercises['true_false'])) {
            $this->scores['true_false'] = 0;
            foreach ($this->exercises['true_false'] as $i => $item) {
                $user_answer = $this->user_answers['true_false'][$i] ?? '';
                $correct_answer = $item['answer'] ?? '';
                if (strcasecmp($user_answer, $correct_answer) === 0) {
                    $this->scores['true_false']++;
                }
            }
            $this->total_score += $this->scores['true_false'];
        }

        // Check matching
        if (! empty($this->exercises['matching'])) {
            $this->scores['matching'] = 0;
            foreach ($this->exercises['matching'] as $i => $item) {
                $user_answer = trim($this->user_answers['matching'][$i] ?? '');
                $correct_answer = trim($item['match'] ?? '');
                if (strcasecmp($user_answer, $correct_answer) === 0) {
                    $this->scores['matching']++;
                }
            }
            $this->total_score += $this->scores['matching'];
        }

        // Check quiz
        if (! empty($this->exercises['quiz'])) {
            $this->scores['quiz'] = 0;
            foreach ($this->exercises['quiz'] as $i => $item) {
                $user_answer = $this->user_answers['quiz'][$i] ?? '';
                $correct_answer = $item['answer'] ?? '';
                if ($user_answer === $correct_answer) {
                    $this->scores['quiz']++;
                }
            }
            $this->total_score += $this->scores['quiz'];
        }

        // Save to database if attempt exists
        if ($this->attempt_id !== null) {
            $this->saveAttemptAndScore();
        }
    }

    protected function saveAttemptAndScore(): void
    {
        $attempt = LessonAttempt::find($this->attempt_id);
        if ($attempt === null) {
            return;
        }

        $grading_service = app(LessonGradingService::class);

        $grading_service->completeAttempt(
            $attempt,
            $this->user_answers,
            $this->scores,
            $this->total_score,
            $this->max_score
        );

        // Update assignment status if exists
        if ($attempt->assignment_id !== null) {
            $assignment = $attempt->assignment;
            if ($assignment !== null) {
                $grading_service->updateAssignmentStatus($assignment);
            }
        }
    }

    public function resetExercises(): void
    {
        $this->checked = false;
        $this->current_exercise_index = 0;
        $this->user_answers = [
            'gap_fill' => [],
            'sentence_ordering' => [],
            'true_false' => [],
            'matching' => [],
            'quiz' => [],
        ];
        $this->scores = [
            'gap_fill' => 0,
            'sentence_ordering' => 0,
            'true_false' => 0,
            'matching' => 0,
            'quiz' => 0,
        ];
        $this->total_score = 0;

        // Reset ordered_words
        if (! empty($this->exercises['sentence_ordering'])) {
            foreach ($this->exercises['sentence_ordering'] as $index => $item) {
                if (isset($item['words']) && is_array($item['words'])) {
                    $this->ordered_words[$index] = $item['words'];
                }
            }
        }

        // Start new attempt
        if ($this->lesson_id !== null && Auth::check()) {
            $this->startAttempt();
        }
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.lesson.lesson-exercises');
    }
}
