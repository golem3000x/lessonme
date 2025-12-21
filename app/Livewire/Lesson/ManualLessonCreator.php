<?php

declare(strict_types=1);

namespace App\Livewire\Lesson;

use App\Enums\Lesson\LessonCreationTypeEnum;
use App\Enums\System\SystemLanguageEnum;
use App\Models\Keyword\Keyword;
use App\Models\Lesson\Lesson;
use App\Models\Topic\Topic;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.app-layout')]
class ManualLessonCreator extends Component
{
    public string $active_tab = 'basic';

    // Basic info
    public string $lesson_name = '';

    public string $lesson_text = '';

    public ?int $topic_id = null;

    public string $language_level = 'A1';

    public string $lesson_language = '';

    /** @var array<int,array{id:int,label:string}> */
    public array $topics = [];

    /** @var array<int,array{id:int,label:string}> */
    public array $keywords = [];

    /** @var array<int,int> */
    public array $keyword_ids = [];

    /** @var array<int,array{value:string,label:string,flag:string}> */
    public array $available_languages = [];

    // Vocabulary
    /** @var array<int,array{word:string,translation:string,example:string}> */
    public array $vocabulary = [];

    // Grammar
    /** @var array<int,array{phrase:string,label:string,explanation:string,formula:string,example:string,breakdown:string}> */
    public array $grammar_data = [];

    // Dialogue
    /** @var array<int,array{speaker:string,line:string}> */
    public array $dialogue = [];

    // Exercises
    /** @var array<int,array{sentence:string,answer:string}> */
    public array $gap_fill = [];

    /** @var array<int,array{words:array<int,string>,answer:string}> */
    public array $sentence_ordering = [];

    /** @var array<int,array{statement:string,answer:string}> */
    public array $true_false = [];

    /** @var array<int,array{word:string,match:string}> */
    public array $matching = [];

    /** @var array<int,array{question:string,options:array<int,string>,answer:string}> */
    public array $quiz = [];

    // Other
    public string $homework = '';

    public string $culture_notes = '';

    /** @var array<int,array{mistake:string,correction:string,explanation:string}> */
    public array $common_mistakes = [];

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $locale = app()->getLocale();

        // Load topics
        $topics_collection = Topic::with(['translations'])
            ->visibleForUser($user->id)
            ->get();

        $this->topics = $topics_collection->map(fn (Topic $t) => [
            'id' => $t->id,
            'label' => $t->title($locale),
        ])->values()->toArray();

        if (! empty($this->topics)) {
            $this->topic_id = $this->topics[0]['id'];
            $this->loadKeywords();
        }

        // Load available languages
        $learning_languages = $user->getLearningLanguageValues();
        if (empty($learning_languages)) {
            $learning_languages = SystemLanguageEnum::values();
        }

        $this->available_languages = collect($learning_languages)
            ->map(function (string $lang_value) {
                $enum = SystemLanguageEnum::tryFrom($lang_value);

                return $enum ? [
                    'value' => $enum->value,
                    'label' => $enum->label(),
                    'flag' => $enum->flag(),
                ] : null;
            })
            ->filter()
            ->values()
            ->toArray();

        if (! empty($this->available_languages)) {
            $this->lesson_language = $this->available_languages[0]['value'];
        }
    }

    public function updatedTopicId(): void
    {
        $this->loadKeywords();
        $this->keyword_ids = [];
    }

    protected function loadKeywords(): void
    {
        if (! $this->topic_id) {
            $this->keywords = [];

            return;
        }

        $user = Auth::user();
        if ($user === null) {
            return;
        }
        $locale = app()->getLocale();

        $keywords_collection = Keyword::with('translations')
            ->where('topic_id', $this->topic_id)
            ->visibleForUser($user->id)
            ->get();

        $this->keywords = $keywords_collection->map(fn (Keyword $k) => [
            'id' => $k->id,
            'label' => $k->value($locale),
        ])->values()->toArray();
    }

    public function setActiveTab(string $tab): void
    {
        $this->active_tab = $tab;
    }

    // Vocabulary methods
    public function addVocabularyItem(): void
    {
        $this->vocabulary[] = ['word' => '', 'translation' => '', 'example' => ''];
    }

    public function removeVocabularyItem(int $index): void
    {
        unset($this->vocabulary[$index]);
        $this->vocabulary = array_values($this->vocabulary);
    }

    // Grammar methods
    public function addGrammarItem(): void
    {
        $this->grammar_data[] = [
            'phrase' => '',
            'label' => '',
            'explanation' => '',
            'formula' => '',
            'example' => '',
            'breakdown' => '',
        ];
    }

    public function removeGrammarItem(int $index): void
    {
        unset($this->grammar_data[$index]);
        $this->grammar_data = array_values($this->grammar_data);
    }

    // Dialogue methods
    public function addDialogueLine(): void
    {
        $last_speaker = ! empty($this->dialogue) ? end($this->dialogue)['speaker'] : 'B';
        $next_speaker = $last_speaker === 'A' ? 'B' : 'A';
        $this->dialogue[] = ['speaker' => $next_speaker, 'line' => ''];
    }

    public function removeDialogueLine(int $index): void
    {
        unset($this->dialogue[$index]);
        $this->dialogue = array_values($this->dialogue);
    }

    // Exercise methods - Gap Fill
    public function addGapFill(): void
    {
        if (count($this->gap_fill) < 7) {
            $this->gap_fill[] = ['sentence' => '', 'answer' => ''];
        }
    }

    public function removeGapFill(int $index): void
    {
        unset($this->gap_fill[$index]);
        $this->gap_fill = array_values($this->gap_fill);
    }

    // Exercise methods - Sentence Ordering
    public function addSentenceOrdering(): void
    {
        if (count($this->sentence_ordering) < 6) {
            $this->sentence_ordering[] = ['words' => [], 'answer' => ''];
        }
    }

    public function removeSentenceOrdering(int $index): void
    {
        unset($this->sentence_ordering[$index]);
        $this->sentence_ordering = array_values($this->sentence_ordering);
    }

    // Exercise methods - True/False
    public function addTrueFalse(): void
    {
        if (count($this->true_false) < 7) {
            $this->true_false[] = ['statement' => '', 'answer' => 'True'];
        }
    }

    public function removeTrueFalse(int $index): void
    {
        unset($this->true_false[$index]);
        $this->true_false = array_values($this->true_false);
    }

    // Exercise methods - Matching
    public function addMatching(): void
    {
        if (count($this->matching) < 9) {
            $this->matching[] = ['word' => '', 'match' => ''];
        }
    }

    public function removeMatching(int $index): void
    {
        unset($this->matching[$index]);
        $this->matching = array_values($this->matching);
    }

    // Exercise methods - Quiz
    public function addQuiz(): void
    {
        if (count($this->quiz) < 6) {
            $this->quiz[] = [
                'question' => '',
                'options' => ['', '', '', ''],
                'answer' => 'A',
            ];
        }
    }

    public function removeQuiz(int $index): void
    {
        unset($this->quiz[$index]);
        $this->quiz = array_values($this->quiz);
    }

    // Common mistakes
    public function addCommonMistake(): void
    {
        $this->common_mistakes[] = ['mistake' => '', 'correction' => '', 'explanation' => ''];
    }

    public function removeCommonMistake(int $index): void
    {
        unset($this->common_mistakes[$index]);
        $this->common_mistakes = array_values($this->common_mistakes);
    }

    public function saveDraft(): void
    {
        $this->saveLesson(true);
    }

    public function publishLesson(): void
    {
        $this->saveLesson(false);
    }

    protected function saveLesson(bool $is_draft): void
    {
        $this->validate([
            'lesson_name' => 'required|string|max:255',
            'topic_id' => 'required|integer|exists:topics,id',
            'language_level' => 'required|string',
            'lesson_language' => 'required|string',
        ]);

        $user = Auth::user();
        if ($user === null) {
            return;
        }

        $exercises = [
            'gap_fill' => array_filter($this->gap_fill, fn ($item) => ! empty($item['sentence'])),
            'sentence_ordering' => array_filter($this->sentence_ordering, fn ($item) => ! empty($item['answer'])),
            'true_false' => array_filter($this->true_false, fn ($item) => ! empty($item['statement'])),
            'matching' => array_filter($this->matching, fn ($item) => ! empty($item['word'])),
            'quiz' => array_filter($this->quiz, fn ($item) => ! empty($item['question'])),
        ];

        $lesson = Lesson::create([
            'uuid' => Str::uuid()->toString(),
            'user_id' => $user->id,
            'topic_id' => $this->topic_id,
            'lesson_name' => $this->lesson_name,
            'lesson_text' => $this->lesson_text ?: null,
            'language_level' => $this->language_level,
            'lesson_lang' => $this->lesson_language,
            'creation_type' => LessonCreationTypeEnum::MANUAL->value,
            'is_draft' => $is_draft,
            'vocabulary' => array_filter($this->vocabulary, fn ($item) => ! empty($item['word'])) ?: null,
            'grammar_data' => array_filter($this->grammar_data, fn ($item) => ! empty($item['phrase'])) ?: null,
            'dialogue' => array_filter($this->dialogue, fn ($item) => ! empty($item['line'])) ?: null,
            'exercises' => ! empty(array_filter($exercises)) ? $exercises : null,
            'homework' => $this->homework ?: null,
            'common_mistakes' => array_filter($this->common_mistakes, fn ($item) => ! empty($item['mistake'])) ?: null,
            'culture_notes' => $this->culture_notes ?: null,
        ]);

        session()->flash('message', $is_draft
            ? __('lessons.lesson_saved')
            : __('lessons.lesson_published')
        );

        $this->redirect(route('lessons.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.lesson.manual-lesson-creator');
    }
}
