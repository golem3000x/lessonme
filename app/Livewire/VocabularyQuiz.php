<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class VocabularyQuiz extends Component
{
    public $quiz = [];

    public $answers = [];

    public $checked = false;

    public $score = 0;

    public function mount($quiz): void
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
