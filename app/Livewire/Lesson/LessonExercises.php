<?php

declare(strict_types=1);

namespace App\Livewire\Lesson;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LessonExercises extends Component
{
    /** @var array<string, mixed> */
    public array $exercises = [];

    /** @var array<string, array<int, string>> */
    public array $user_answers = [
        'gap_fill' => [],
        'sentence_ordering' => [],
        'true_false' => [],
        'matching' => [],
    ];

    /** @var array<string, array<int, string>> */
    public array $ordered_words = [];

    public bool $checked = false;

    /** @var array<string, int> */
    public array $scores = [
        'gap_fill' => 0,
        'sentence_ordering' => 0,
        'true_false' => 0,
        'matching' => 0,
    ];

    public int $total_score = 0;

    public int $max_score = 0;

    /**
     * @param  array<string, mixed>  $exercises
     */
    public function mount(array $exercises): void
    {
        $this->exercises = $exercises;

        // Initialize ordered_words for sentence_ordering exercises
        if (!empty($exercises['sentence_ordering'])) {
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
            + count($exercises['matching'] ?? []);
    }

    public function moveWord(int $exercise_index, int $from_index, string $direction): void
    {
        if (!isset($this->ordered_words[$exercise_index])) {
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

    public function checkAnswers(): void
    {
        $this->checked = true;
        $this->total_score = 0;

        // Check gap_fill
        if (!empty($this->exercises['gap_fill'])) {
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
        if (!empty($this->exercises['sentence_ordering'])) {
            $this->scores['sentence_ordering'] = 0;
            foreach ($this->exercises['sentence_ordering'] as $i => $item) {
                $user_answer = implode(' ', $this->ordered_words[$i] ?? []);
                $correct_answer = trim($item['answer'] ?? '');
                if (strcasecmp($user_answer, $correct_answer) === 0) {
                    $this->scores['sentence_ordering']++;
                }
            }
            $this->total_score += $this->scores['sentence_ordering'];
        }

        // Check true_false
        if (!empty($this->exercises['true_false'])) {
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
        if (!empty($this->exercises['matching'])) {
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
    }

    public function resetExercises(): void
    {
        $this->checked = false;
        $this->user_answers = [
            'gap_fill' => [],
            'sentence_ordering' => [],
            'true_false' => [],
            'matching' => [],
        ];
        $this->scores = [
            'gap_fill' => 0,
            'sentence_ordering' => 0,
            'true_false' => 0,
            'matching' => 0,
        ];
        $this->total_score = 0;

        // Reset ordered_words
        if (!empty($this->exercises['sentence_ordering'])) {
            foreach ($this->exercises['sentence_ordering'] as $index => $item) {
                if (isset($item['words']) && is_array($item['words'])) {
                    $this->ordered_words[$index] = $item['words'];
                }
            }
        }
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.lesson.lesson-exercises');
    }
}
