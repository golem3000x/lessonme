<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

use App\Models\Keyword\Keyword;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class KeywordSelect extends Component
{
    /**
     * Parent-selected topic id.
     */
    public ?int $topic_id = null;

    /**
     * Selected keyword ids inside this component.
     *
     * @var array<int,int>
     */
    public array $keyword_ids = [];

    /** @var array<int,array{id:int,label:string}> */
    public array $keywords = [];

    public function mount(?int $topic_id = null, ?array $keyword_ids = null, ?array $keywords = null): void
    {
        $this->topic_id = $topic_id;
        $this->keyword_ids = array_values(array_unique(array_map('intval', $keyword_ids ?? [])));
        
        // If keywords are preloaded from parent, use them
        if ($keywords !== null && is_array($keywords)) {
            $this->keywords = $keywords;
        } else {
            // Fallback: if parent doesn't preload, fetch locally
            $this->loadKeywords(reset_selection: false);
        }
    }

    public function updatedTopicId(int|string|null $value): void
    {
        $this->topic_id = $value !== null && $value !== '' ? (int) $value : null;
        // Parent is responsible for preloading keywords and sending them via event.
    }

    /**
     * Toggle a keyword in the selection.
     */
    public function toggleKeyword(int $keyword_id): void
    {
        $available = collect($this->keywords)->pluck('id');
        
        if (!$available->contains($keyword_id)) {
            return;
        }

        $index = array_search($keyword_id, $this->keyword_ids, true);
        
        if ($index !== false) {
            // Remove from selection
            unset($this->keyword_ids[$index]);
            $this->keyword_ids = array_values($this->keyword_ids);
        } else {
            // Add to selection
            $this->keyword_ids[] = $keyword_id;
        }
        
        $this->dispatch('keywords-selected', ids: $this->keyword_ids);
    }

    /**
     * Livewire updates array from <select multiple> as array of selected values.
     *
     * @param  array<int,int|string>|null  $value
     */
    public function updatedKeywordIds($value): void
    {
        // Normalize to array because Livewire may send a single scalar when only one option is selected
        $incoming = is_array($value) ? $value : ($value !== null && $value !== '' ? [$value] : []);

        $ids = collect($incoming)
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values();

        $available = collect($this->keywords)->pluck('id');
        $this->keyword_ids = $ids->filter(fn (int $id) => $available->contains($id))->values()->all();

        $this->dispatch('keywords-selected', ids: $this->keyword_ids);
    }

    protected function loadKeywords(bool $reset_selection = false): void
    {
        $user = Auth::user();
        $locale = app()->getLocale();

        if (! $this->topic_id) {
            $this->keywords = [];
            $this->keyword_ids = [];
            $this->dispatch('keywords-selected', ids: []);

            return;
        }

        $keywords = Keyword::with('translations')
            ->where('topic_id', '=', $this->topic_id)
            ->visibleForUser($user->id)
            ->get();

        $this->keywords = $keywords->map(function (Keyword $keyword) use ($locale) {
            return [
                'id' => $keyword->id,
                'label' => $keyword->value($locale),
            ];
        })->values()->toArray();

        $available = collect($this->keywords)->pluck('id');

        if ($reset_selection) {
            $this->keyword_ids = [];
        } else {
            $this->keyword_ids = collect($this->keyword_ids)
                ->filter(fn (int $id) => $available->contains($id))
                ->values()
                ->all();
        }

        $this->dispatch('keywords-selected', ids: $this->keyword_ids);
    }

    #[On('topic-changed')]
    public function onTopicChanged(?int $id): void
    {
        $this->topic_id = $id;
        // Parent will send preloaded keywords separately.
    }

    /**
     * Receive preloaded keywords from parent component to avoid duplicate DB queries.
     *
     * @param  array<int,array{id:int,label:string}>  $keywords
     * @param  array<int,int>|null  $selectedIds
     */
    #[On('keywords-preloaded')]
    public function onKeywordsPreloaded(array $keywords, bool $reset = false, ?array $selectedIds = null): void
    {
        $this->keywords = $keywords;

        $available = collect($this->keywords)->pluck('id');

        if ($reset) {
            $this->keyword_ids = [];
        } else {
            $incoming = collect($selectedIds ?? $this->keyword_ids)
                ->map(fn ($v) => (int) $v)
                ->unique();

            $this->keyword_ids = $incoming
                ->filter(fn (int $id) => $available->contains($id))
                ->values()
                ->all();
        }

        $this->dispatch('keywords-selected', ids: $this->keyword_ids);
    }

    public function render(): Factory|View
    {
        return view('livewire.chat.keyword-select');
    }
}
