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

    // Placeholders for topics being created
    /** @var array<int, string> */
    public array $creating_topics_placeholders = [];

    // Placeholders for keywords being created per topic
    /** @var array<int, array<int, string>> */
    public array $creating_keywords_placeholders = [];

    // Active tab: 'system' or 'custom'
    public string $active_tab = 'custom';

    // Filters UI state
    public bool $show_filters = false;

    public bool $show_sort_dropdown = false;

    // Confirmation modals
    public bool $show_delete_topic_confirm = false;

    public ?int $topic_to_delete = null;

    public bool $show_delete_keyword_confirm = false;

    public ?int $keyword_to_delete = null;

    // Filter values (temporary, not applied yet)
    public string $temp_search = '';

    // Applied filter values
    public string $search = '';

    public string $sort = 'newest';

    public function mount(): void
    {
        $this->loadVisibilityState();

        // Restore filters and tab from session
        $this->active_tab = session('topic_filters.active_tab', 'custom');
        $this->search = session('topic_filters.search', '');
        $this->sort = session('topic_filters.sort', 'newest');

        // Initialize temp filters with applied values
        $this->temp_search = $this->search;
    }

    public function updated(string $property): void
    {
        // Save sort to session when it changes
        if ($property === 'sort') {
            session(['topic_filters.sort' => $this->sort]);
            $this->resetPage();
        }
    }

    public function setSort(string $sort_value): void
    {
        $this->sort = $sort_value;
        session(['topic_filters.sort' => $this->sort]);
        $this->resetPage();
    }

    #[On('topic-creating')]
    public function addTopicPlaceholder(): void
    {
        // Generate unique ID for the placeholder
        $placeholder_id = uniqid('placeholder_', true);
        $this->creating_topics_placeholders[] = $placeholder_id;
    }

    #[On('keyword-creating')]
    public function addKeywordPlaceholder(int $topicId): void
    {
        // Generate unique ID for the placeholder
        $placeholder_id = uniqid('placeholder_', true);
        
        if (!isset($this->creating_keywords_placeholders[$topicId])) {
            $this->creating_keywords_placeholders[$topicId] = [];
        }
        
        $this->creating_keywords_placeholders[$topicId][] = $placeholder_id;
    }

    #[On('topic-created')]
    #[On('keyword-created')]
    public function loadVisibilityState(): void
    {
        /** @var User $user */
        $user = Auth::user();

        // Clear all placeholders when topic/keyword is created
        $this->creating_topics_placeholders = [];
        $this->creating_keywords_placeholders = [];

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

        // Remove from visible topics and expanded arrays if present
        $this->visible_topic_ids = array_values(array_filter(
            $this->visible_topic_ids,
            fn ($id) => $id !== $topic_id
        ));

        $this->expanded_topics = array_values(array_filter(
            $this->expanded_topics,
            fn ($id) => $id !== $topic_id
        ));

        $topic->delete();

        // Reset pagination to avoid issues
        $this->resetPage();

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

        // Remove from visible keywords array if present
        $this->visible_keyword_ids = array_values(array_filter(
            $this->visible_keyword_ids,
            fn ($id) => $id !== $keyword_id
        ));

        $keyword->delete();

        session()->flash('message', __('topics.keyword_deleted'));
    }

    // Modal methods for topic deletion
    public function requestDeleteTopic(int $topic_id): void
    {
        $this->topic_to_delete = $topic_id;
        $this->show_delete_topic_confirm = true;
    }

    public function confirmDeleteTopic(): void
    {
        if ($this->topic_to_delete) {
            $this->deleteTopic($this->topic_to_delete);
        }

        $this->cancelDeleteTopic();
    }

    public function cancelDeleteTopic(): void
    {
        $this->show_delete_topic_confirm = false;
        $this->topic_to_delete = null;
    }

    // Modal methods for keyword deletion
    public function requestDeleteKeyword(int $keyword_id): void
    {
        $this->keyword_to_delete = $keyword_id;
        $this->show_delete_keyword_confirm = true;
    }

    public function confirmDeleteKeyword(): void
    {
        if ($this->keyword_to_delete) {
            $this->deleteKeyword($this->keyword_to_delete);
        }

        $this->cancelDeleteKeyword();
    }

    public function cancelDeleteKeyword(): void
    {
        $this->show_delete_keyword_confirm = false;
        $this->keyword_to_delete = null;
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

    public function switchTab(string $tab): void
    {
        $this->active_tab = $tab;
        
        // Close filters panel and sort dropdown when switching tabs
        $this->show_filters = false;
        $this->show_sort_dropdown = false;
        
        // Save tab to session
        session(['topic_filters.active_tab' => $tab]);
        
        // Reset pagination when switching tabs
        $this->resetPage();
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
        }
    }

    public function applyFilters(): void
    {
        $this->search = $this->temp_search;

        // Save filters to session
        session([
            'topic_filters.search' => $this->search,
        ]);

        $this->resetPage();
        $this->show_filters = false;
    }

    public function resetFilters(): void
    {
        $this->temp_search = '';
        $this->search = '';

        // Clear filters from session (keep active_tab)
        session()->forget(['topic_filters.search']);

        $this->resetPage();
    }

    #[Computed]
    public function filteredTopicsCount(): int
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only count custom topics for filters
        $query = Topic::where('user_id', '=', $user->id);

        // Apply temporary search filter
        if ($this->temp_search) {
            $query->whereHas('translations', function ($q) {
                $q->where('value', 'like', '%'.$this->temp_search.'%');
            });
        }

        return $query->count();
    }

    public function hasActiveFilters(): bool
    {
        return $this->search !== '' || $this->sort !== 'newest';
    }


    public function render(): Factory|View
    {
        /** @var User $user */
        $user = Auth::user();
        
        if ($this->active_tab === 'system') {
            // System topics - no filters, no pagination
            $system_topics = Topic::with([
                'translations',
                'keywords.translations',
            ])
                ->whereNull('user_id')
                ->latest()
                ->get();

            return view('livewire.topic.topic-index', [
                'system_topics' => $system_topics,
                'custom_topics' => collect(),
            ]);
        } else {
            // Custom topics - with filters and pagination
            $query = Topic::with([
                'translations',
                'keywords.translations',
            ])
                ->where('user_id', '=', $user->id);

            // Apply search filter
            if ($this->search) {
                $query->whereHas('translations', function ($q) {
                    $q->where('value', 'like', '%'.$this->search.'%');
                });
            }

            // Apply sorting
            if ($this->sort === 'oldest') {
                $query->oldest();
            } else {
                $query->latest();
            }

            $custom_topics = $query->paginate(12);

            return view('livewire.topic.topic-index', [
                'system_topics' => collect(),
                'custom_topics' => $custom_topics,
            ]);
        }
    }
}
