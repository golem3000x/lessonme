<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

use App\Models\Keyword\Keyword;
use App\Models\Topic\Topic;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatLesson extends Component
{
    public ?int $topic_id = null;

    public string $topic = '';

    /** @var array<int, int> */
    public array $keyword_ids = [];

    /** @var array<int, array{id: int, label: string}> */
    public array $topics = [];

    /** @var array<int, array{id: int, label: string}> */
    public array $keywords = [];

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->topics = [];
            $this->keywords = [];

            return;
        }

        $locale = app()->getLocale();

        // Load visible topics
        $topics_query = Topic::with('translations')
            ->where('is_system', '=', true)
            ->visibleForUser($user->id)
            ->get();

        $this->topics = $topics_query->map(function (Topic $topic) use ($locale): array {
            return [
                'id' => $topic->id,
                'label' => $topic->translatedValue($locale),
            ];
        })->toArray();

        // Try to restore topic from session
        $session_topic_id = session('selected_topic_id');
        if ($session_topic_id) {
            $this->topic_id = (int) $session_topic_id;
            $this->loadTopicLabel();
            $this->loadKeywords();
        }

        // Try to restore keyword selection from session
        $session_keyword_ids = session('selected_keyword_ids');
        if ($session_keyword_ids && is_array($session_keyword_ids)) {
            $this->keyword_ids = array_values(array_map('intval', $session_keyword_ids));
        }
    }

    public function updatedTopicId(?int $value): void
    {
        $this->topic_id = $value;
        $this->keyword_ids = [];

        if ($this->topic_id) {
            session(['selected_topic_id' => $this->topic_id]);
            $this->loadTopicLabel();
            $this->loadKeywords();
        } else {
            session()->forget('selected_topic_id');
            $this->topic = '';
            $this->keywords = [];
        }
    }

    /**
     * @param  array<int, int>  $ids
     */
    public function setKeywordIds(array $ids): void
    {
        $this->keyword_ids = array_values(array_map('intval', $ids));
        session(['selected_keyword_ids' => $this->keyword_ids]);
    }

    protected function loadTopicLabel(): void
    {
        if (! $this->topic_id) {
            $this->topic = '';

            return;
        }

        $topic = Topic::with('translations')->find($this->topic_id);
        $this->topic = $topic ? $topic->translatedValue() : '';
    }

    protected function loadKeywords(): void
    {
        if (! $this->topic_id) {
            $this->keywords = [];

            return;
        }

        $user = Auth::user();
        if (! $user) {
            $this->keywords = [];

            return;
        }

        $locale = app()->getLocale();

        $keywords_query = Keyword::with('translations')
            ->where('topic_id', '=', $this->topic_id)
            ->visibleForUser($user->id)
            ->get();

        $this->keywords = $keywords_query->map(function (Keyword $keyword) use ($locale): array {
            return [
                'id' => $keyword->id,
                'label' => $keyword->translatedValue($locale),
            ];
        })->toArray();
    }

    public function render(): Factory|View
    {
        return view('livewire.chat.chat-lesson');
    }
}
