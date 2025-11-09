<?php

declare(strict_types=1);

namespace App\Livewire\Topic;

use App\Models\Keyword\Keyword;
use App\Models\Keyword\UserKeywordVisibility;
use App\Models\Topic\Topic;
use App\Models\Topic\UserTopicVisibility;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class TopicIndex extends Component
{
    /** @var array<int, array{id:int,label:string,user_id:?int}> */
    public array $available_topics = [];

    /**
     * topic_id => list of keywords [ [id,label,user_id], ... ]
     *
     * @var array<int, array<int, array{id:int,label:string,user_id:?int}>>
     */
    public array $topic_keywords = [];

    /** @var array<int,int> */
    public array $visible_topic_ids = [];

    /** @var array<int,int> */
    public array $visible_keyword_ids = [];

    public function mount(): void
    {
        $this->loadTopicsAndKeywords();
    }

    #[On('topic-created')]
    #[On('keyword-created')]
    public function loadTopicsAndKeywords(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $locale = app()->getLocale();
        $topics = Topic::with([
            'translations',
            'visibility' => function ($q) use ($user): void {
                $q->where('user_id', '=', $user->id);
            },
            'keywords.translations',
            'keywords.visibility' => function ($q) use ($user): void {
                $q->where('user_id', '=', $user->id);
            },
        ])
            ->where(function ($q) use ($user): void {
                $q->whereNull('user_id')->orWhere('user_id', '=', $user->id);
            })
            ->get();

        $this->available_topics = $topics->map(function (Topic $topic) use ($locale) {
            return [
                'id' => $topic->id,
                'label' => $topic->title($locale),
                'user_id' => $topic->user_id,
            ];
        })->values()->toArray();

        $this->visible_topic_ids = $topics->filter(function (Topic $topic) {
            $record = $topic->visibility->first();

            return ! ($record && $record->is_hidden === true);
        })->pluck('id')->values()->all();

        $this->topic_keywords = [];
        $visible_keyword_ids = [];
        foreach ($topics as $topic) {
            $this->topic_keywords[$topic->id] = $topic->keywords->map(function (Keyword $kw) use ($locale) {
                return [
                    'id' => $kw->id,
                    'label' => $kw->value($locale),
                    'user_id' => $kw->user_id,
                ];
            })->values()->toArray();

            foreach ($topic->keywords as $kw) {
                $record = $kw->visibility->first();
                if (! ($record && $record->is_hidden === true)) {
                    $visible_keyword_ids[] = $kw->id;
                }
            }
        }
        $this->visible_keyword_ids = array_values(array_unique(array_map('intval', $visible_keyword_ids)));
    }

    public function deleteTopic(int $topic_id): void
    {
        /** @var User $user */
        $user = Auth::user();

        $topic = Topic::where('id', $topic_id)
            ->where('user_id', $user->id)
            ->first();

        if (! $topic) {
            return;
        }

        $topic->delete();

        // Remove from available topics
        $this->available_topics = array_values(array_filter(
            $this->available_topics,
            fn ($t) => $t['id'] !== $topic_id
        ));

        // Remove topic keywords
        unset($this->topic_keywords[$topic_id]);

        session()->flash('message', __('topics.topic_deleted'));
    }

    public function deleteKeyword(int $keyword_id): void
    {
        /** @var User $user */
        $user = Auth::user();

        $keyword = Keyword::where('id', $keyword_id)
            ->where('user_id', $user->id)
            ->first();

        if (! $keyword) {
            return;
        }

        $topic_id = $keyword->topic_id;
        $keyword->delete();

        // Remove from topic_keywords
        if (isset($this->topic_keywords[$topic_id])) {
            $this->topic_keywords[$topic_id] = array_values(array_filter(
                $this->topic_keywords[$topic_id],
                fn ($k) => $k['id'] !== $keyword_id
            ));
        }

        session()->flash('message', __('topics.keyword_deleted'));
    }

    public function saveVisibility(): void
    {
        /** @var User $user */
        $user = Auth::user();

        // Persist topic visibility preferences
        $selected_topic_ids = array_values(array_unique(array_map('intval', $this->visible_topic_ids)));
        $all_topic_ids = array_values(array_unique(array_map(fn ($t) => (int) $t['id'], $this->available_topics)));

        foreach ($all_topic_ids as $topic_id) {
            $is_visible = in_array($topic_id, $selected_topic_ids, true);
            UserTopicVisibility::updateOrCreate(
                ['user_id' => $user->id, 'topic_id' => $topic_id],
                ['is_hidden' => ! $is_visible]
            );
        }

        // Persist keyword visibility preferences
        $selected_keyword_ids = array_values(array_unique(array_map('intval', $this->visible_keyword_ids)));
        $all_keyword_ids = [];
        foreach ($this->topic_keywords as $topic_id => $keywords) {
            foreach ($keywords as $k) {
                $all_keyword_ids[] = (int) $k['id'];
            }
        }
        $all_keyword_ids = array_values(array_unique($all_keyword_ids));

        foreach ($all_keyword_ids as $kid) {
            $is_visible = in_array($kid, $selected_keyword_ids, true);
            UserKeywordVisibility::updateOrCreate(
                ['user_id' => $user->id, 'keyword_id' => $kid],
                ['is_hidden' => ! $is_visible]
            );
        }

        session()->flash('message', __('topics.visibility_saved'));
    }

    public function render(): Factory|View
    {
        return view('livewire.topic.topic-index');
    }
}
