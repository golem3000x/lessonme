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
use Livewire\WithPagination;

class TopicIndex extends Component
{
    use WithPagination;

    /** @var array<int,int> */
    public array $visible_topic_ids = [];

    /** @var array<int,int> */
    public array $visible_keyword_ids = [];

    /** @var array<int,int> */
    public array $expanded_topics = [];

    public function mount(): void
    {
        $this->loadVisibilityState();
    }

    #[On('topic-created')]
    #[On('keyword-created')]
    public function loadVisibilityState(): void
    {
        /** @var User $user */
        $user = Auth::user();

        // Load all topic visibility states
        $topic_visibility = UserTopicVisibility::where('user_id', $user->id)
            ->where('is_hidden', false)
            ->pluck('topic_id')
            ->toArray();

        // Also include topics without explicit visibility record (default visible)
        $topics_with_visibility = UserTopicVisibility::where('user_id', $user->id)
            ->pluck('topic_id')
            ->toArray();

        $all_topic_ids = Topic::where(function ($q) use ($user): void {
                $q->whereNull('user_id')->orWhere('user_id', '=', $user->id);
            })
            ->pluck('id')
            ->toArray();

        $topics_without_record = array_diff($all_topic_ids, $topics_with_visibility);

        $this->visible_topic_ids = array_values(array_merge($topic_visibility, $topics_without_record));

        // Load all keyword visibility states
        $keyword_visibility = UserKeywordVisibility::where('user_id', $user->id)
            ->where('is_hidden', false)
            ->pluck('keyword_id')
            ->toArray();

        $keywords_with_visibility = UserKeywordVisibility::where('user_id', $user->id)
            ->pluck('keyword_id')
            ->toArray();

        $all_keyword_ids = Keyword::whereHas('topic', function ($q) use ($user) {
                $q->where(function ($q2) use ($user): void {
                    $q2->whereNull('user_id')->orWhere('user_id', '=', $user->id);
                });
            })
            ->pluck('id')
            ->toArray();

        $keywords_without_record = array_diff($all_keyword_ids, $keywords_with_visibility);

        $this->visible_keyword_ids = array_values(array_merge($keyword_visibility, $keywords_without_record));
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

        $keyword->delete();

        session()->flash('message', __('topics.keyword_deleted'));
    }

    public function toggleTopicVisibility(int $topic_id): void
    {
        /** @var User $user */
        $user = Auth::user();

        $is_currently_visible = in_array($topic_id, $this->visible_topic_ids, true);
        $new_visibility = ! $is_currently_visible;

        // Update local state
        if ($new_visibility) {
            $this->visible_topic_ids[] = $topic_id;
        } else {
            $this->visible_topic_ids = array_values(array_filter(
                $this->visible_topic_ids,
                fn ($id) => $id !== $topic_id
            ));
        }

        // Persist immediately
        UserTopicVisibility::updateOrCreate(
            ['user_id' => $user->id, 'topic_id' => $topic_id],
            ['is_hidden' => ! $new_visibility]
        );
    }

    public function toggleKeywordVisibility(int $keyword_id): void
    {
        /** @var User $user */
        $user = Auth::user();

        $is_currently_visible = in_array($keyword_id, $this->visible_keyword_ids, true);
        $new_visibility = ! $is_currently_visible;

        // Update local state
        if ($new_visibility) {
            $this->visible_keyword_ids[] = $keyword_id;
        } else {
            $this->visible_keyword_ids = array_values(array_filter(
                $this->visible_keyword_ids,
                fn ($id) => $id !== $keyword_id
            ));
        }

        // Persist immediately
        UserKeywordVisibility::updateOrCreate(
            ['user_id' => $user->id, 'keyword_id' => $keyword_id],
            ['is_hidden' => ! $new_visibility]
        );
    }

    public function toggleAccordion(int $topic_id): void
    {
        $index = array_search($topic_id, $this->expanded_topics, true);

        if ($index !== false) {
            // Collapse
            unset($this->expanded_topics[$index]);
            $this->expanded_topics = array_values($this->expanded_topics);
        } else {
            // Expand
            $this->expanded_topics[] = $topic_id;
        }
    }

    public function render(): Factory|View
    {
        /** @var User $user */
        $user = Auth::user();
        
        $locale = app()->getLocale();
        $topics = Topic::with([
            'translations',
            'keywords.translations',
        ])
            ->where(function ($q) use ($user): void {
                $q->whereNull('user_id')->orWhere('user_id', '=', $user->id);
            })
            ->paginate(12);

        return view('livewire.topic.topic-index', [
            'topics' => $topics,
        ]);
    }
}
