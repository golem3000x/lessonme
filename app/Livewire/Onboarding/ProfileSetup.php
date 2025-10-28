<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Enums\SystemLanguageEnum;
use App\Jobs\ModerateTopicJob;
use App\Models\Keyword\Keyword;
use App\Models\Keyword\KeywordTranslation;
use App\Models\Keyword\UserKeywordVisibility;
use App\Models\Topic\Topic;
use App\Models\Topic\UserTopicVisibility;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class ProfileSetup extends Component
{
    public string $name = '';

    public string $native_lang;

    public string $learning_lang;

    public string $preferred_time = '19:00';

    public string $new_topic_name = '';

    /** @var SystemLanguageEnum[] */
    public array $languages = [];

    /** @var array<int, array{id:int,label:string}> */
    public array $available_topics = [];

    /** @var array<int,int> */
    public array $visible_topic_ids = [];

    /**
     * topic_id => list of keywords [ [id,label], ... ]
     *
     * @var array<int, array<int, array{id:int,label:string}>>
     */
    public array $topic_keywords = [];

    /** @var array<int,int> */
    public array $visible_keyword_ids = [];

    /** @var array<int,string> topic_id => new keyword value */
    public array $new_keyword_by_topic = [];

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->name = $user->name ?? '';
        $this->native_lang = $user->native_lang;
        $this->learning_lang = $user->learning_lang;

        $this->languages = SystemLanguageEnum::all();

        // Preload topics available to the user (system + user-created), regardless of current visibility
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
            ->whereNull('deleted_at')
            ->where(function ($q) use ($user): void {
                $q->whereNull('user_id')->orWhere('user_id', '=', $user->id);
            })
            ->get();

        $this->available_topics = $topics->map(function (Topic $topic) use ($locale) {
            return [
                'id' => $topic->id,
                'label' => $topic->title($locale),
            ];
        })->values()->toArray();

        $this->visible_topic_ids = $topics->filter(function (Topic $topic) {
            $record = $topic->visibility->first();

            return ! ($record && $record->is_hidden === true);
        })->pluck('id')->values()->all();

        // Prepare per-topic keywords and visibility list
        $this->topic_keywords = [];
        $visible_keyword_ids = [];
        foreach ($topics as $topic) {
            $this->topic_keywords[$topic->id] = $topic->keywords->map(function (Keyword $kw) use ($locale) {
                return [
                    'id' => $kw->id,
                    'label' => $kw->value($locale),
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

    public function addKeyword(int $topic_id): void
    {
        $value = trim($this->new_keyword_by_topic[$topic_id] ?? '');
        if ($value === '') {
            return;
        }

        /** @var User $user */
        $user = Auth::user();

        $base_slug = Str::slug($value);
        $slug = $base_slug;
        $i = 1;
        while (Keyword::where('slug', $slug)->exists()) {
            $slug = $base_slug.'-'.$i;
            $i++;
        }

        $keyword = Keyword::create([
            'user_id' => $user->id,
            'topic_id' => $topic_id,
            'slug' => $slug,
            'is_system' => false,
        ]);

        KeywordTranslation::create([
            'keyword_id' => $keyword->id,
            'language_code' => $this->native_lang,
            'value' => $value,
        ]);

        // Ensure it's visible by default for the creator
        UserKeywordVisibility::updateOrCreate(
            ['user_id' => $user->id, 'keyword_id' => $keyword->id],
            ['is_hidden' => false]
        );

        // Update component state
        $this->topic_keywords[$topic_id][] = [
            'id' => $keyword->id,
            'label' => $value,
        ];
        $this->visible_keyword_ids[] = $keyword->id;
        $this->visible_keyword_ids = array_values(array_unique(array_map('intval', $this->visible_keyword_ids)));
        $this->new_keyword_by_topic[$topic_id] = '';
    }

    public function save(): Redirector
    {
        /** @var User $user */
        $user = Auth::user();

        $user->update([
            'name' => $this->name,
            'native_lang' => $this->native_lang,
            'learning_lang' => $this->learning_lang,
        ]);

        if (trim($this->new_topic_name) !== '') {
            $slug = Str::slug($this->new_topic_name);

            $topic = Topic::create([
                'user_id' => $user->id,
                'slug' => $slug,
                'is_system' => false,
                'is_approved' => false,
            ]);

            $topic->translations()->create([
                'language_code' => $this->native_lang,
                'value' => $this->new_topic_name,
            ]);

            dispatch(new ModerateTopicJob($topic));
        }

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

        session()->flash('message', __('onboarding.profile_saved'));

        return redirect()->route('onboarding.topics');
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.onboarding.profile-setup');
    }
}
