<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class VocabularyQuiz extends Component
{
    /** @var array<int, array<string, mixed>> */
    public array $quiz = [];

    /** @var array<int, string> */
    public array $answers = [];

    public bool $checked = false;

    public int $score = 0;

    /**
     * @param  array<int, array<string, mixed>>  $quiz
     */
    public function mount(array $quiz): void
    {
        $this->quiz = $quiz;
    }

    public function checkAnswers(): void
    {
        $this->checked = true;
        $this->score = 0;
        foreach ($this->quiz as $i => $q) {
            if (($this->answers[$i] ?? null) === ($q['answer'] ?? null)) {
                $this->score++;
            }
        }
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.vocabulary-quiz');
    }
}
