<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Enums\SystemLanguageEnum;
use App\Jobs\ModerateTopicJob;
use App\Models\Topic\Topic;
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

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->name = $user->name ?? '';
        $this->native_lang = $user->native_lang ?? SystemLanguageEnum::VIETNAMESE->value;
        $this->learning_lang = $user->learning_lang ?? SystemLanguageEnum::ENGLISH->value;

        $this->languages = SystemLanguageEnum::all();
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
                'language_code' => $this->learning_lang,
                'value' => $this->new_topic_name,
            ]);

            dispatch(new ModerateTopicJob($topic));
        }

        session()->flash('message', __('onboarding.profile_saved'));

        return redirect()->route('onboarding.topics');
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.onboarding.profile-setup');
    }
}
