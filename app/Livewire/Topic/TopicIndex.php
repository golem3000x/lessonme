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
use Livewire\Attributes\Computed;
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

    // Filters UI state
    public bool $show_filters = false;

    public bool $show_status_dropdown = false;

    // Filter values (temporary, not applied yet)
    public string $temp_search = '';

    /** @var array<int, string> */
    public array $temp_statuses = [];

    public string $temp_sort = 'newest';

    // Applied filter values
    public string $search = '';

    /** @var array<int, string> */
    public array $statuses = [];

    public string $sort = 'newest';

    public function mount(): void
    {
        $this->loadVisibilityState();

        // Restore filters from session
        $this->search = session('topic_filters.search', '');
        $this->statuses = session('topic_filters.statuses', []);
        $this->sort = session('topic_filters.sort', 'newest');

        // Initialize temp filters with applied values
        $this->temp_search = $this->search;
        $this->temp_statuses = $this->statuses;
        $this->temp_sort = $this->sort;
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

    public function toggleFilters(): void
    {
        $this->show_filters = ! $this->show_filters;

        // Sync temp filters with current applied filters when opening
        if ($this->show_filters) {
            $this->temp_search = $this->search;
            $this->temp_statuses = $this->statuses;
            $this->temp_sort = $this->sort;
        }

        // Close status dropdown when closing filters panel
        if (! $this->show_filters) {
            $this->show_status_dropdown = false;
        }
    }

    public function applyFilters(): void
    {
        $this->search = $this->temp_search;
        $this->statuses = $this->temp_statuses;
        $this->sort = $this->temp_sort;

        // Save filters to session
        session([
            'topic_filters.search' => $this->search,
            'topic_filters.statuses' => $this->statuses,
            'topic_filters.sort' => $this->sort,
        ]);

        $this->resetPage();
        $this->show_filters = false;
        $this->show_status_dropdown = false;
    }

    public function resetFilters(): void
    {
        $this->temp_search = '';
        $this->temp_statuses = [];
        $this->temp_sort = 'newest';

        $this->search = '';
        $this->statuses = [];
        $this->sort = 'newest';

        // Clear filters from session
        session()->forget('topic_filters');

        $this->resetPage();
        $this->show_status_dropdown = false;
    }

    #[Computed]
    public function filteredTopicsCount(): int
    {
        /** @var User $user */
        $user = Auth::user();
        
        $query = Topic::where(function ($q) use ($user): void {
            $q->whereNull('user_id')->orWhere('user_id', '=', $user->id);
        });

        // Apply temporary search filter
        if ($this->temp_search) {
            $query->whereHas('translations', function ($q) {
                $q->where('value', 'like', '%'.$this->temp_search.'%');
            });
        }

        // Apply temporary status filter
        if (! empty($this->temp_statuses)) {
            $query->where(function ($q) use ($user) {
                foreach ($this->temp_statuses as $status) {
                    if ($status === 'system') {
                        $q->orWhereNull('user_id');
                    } elseif ($status === 'user') {
                        $q->orWhere('user_id', '=', $user->id);
                    }
                }
            });
        }

        return $query->count();
    }

    public function hasActiveFilters(): bool
    {
        return $this->search !== '' || ! empty($this->statuses) || $this->sort !== 'newest';
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function getStatusOptions(): array
    {
        return [
            ['value' => 'system', 'label' => __('topics.system')],
            ['value' => 'user', 'label' => __('topics.custom')],
        ];
    }

    public function render(): Factory|View
    {
        /** @var User $user */
        $user = Auth::user();
        
        $locale = app()->getLocale();
        $query = Topic::with([
            'translations',
            'keywords.translations',
        ])
            ->where(function ($q) use ($user): void {
                $q->whereNull('user_id')->orWhere('user_id', '=', $user->id);
            });

        // Apply search filter
        if ($this->search) {
            $query->whereHas('translations', function ($q) {
                $q->where('value', 'like', '%'.$this->search.'%');
            });
        }

        // Apply status filter
        if (! empty($this->statuses)) {
            $query->where(function ($q) use ($user) {
                foreach ($this->statuses as $status) {
                    if ($status === 'system') {
                        $q->orWhereNull('user_id');
                    } elseif ($status === 'user') {
                        $q->orWhere('user_id', '=', $user->id);
                    }
                }
            });
        }

        // Apply sorting
        if ($this->sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $topics = $query->paginate(12);

        return view('livewire.topic.topic-index', [
            'topics' => $topics,
        ]);
    }
}
