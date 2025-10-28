<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Enums\SystemLanguageEnum;
use App\Jobs\ModerateTopicJob;
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
        $selected_ids = array_values(array_unique(array_map('intval', $this->visible_topic_ids)));
        $all_ids = array_values(array_unique(array_map(fn ($t) => (int) $t['id'], $this->available_topics)));

        foreach ($all_ids as $topic_id) {
            $is_visible = in_array($topic_id, $selected_ids, true);
            UserTopicVisibility::updateOrCreate(
                ['user_id' => $user->id, 'topic_id' => $topic_id],
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
