<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Models\Topic\Topic;
use App\Models\User;
use App\Notifications\Slug\Processing\SlugProcessingNotification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class CreateTopic extends Component
{
    public string $topic_name = '';

    public string $native_lang = '';

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->native_lang = $user->native_lang;
    }

    public function saveTopic(): void
    {
        $value = trim($this->topic_name);
        if ($value === '') {
            return;
        }

        /** @var User $user */
        $user = Auth::user();

        $slug = Str::slug($value);

        // Validate uniqueness of topic slug for current user
        $this->validate([
            'topic_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($slug, $user) {
                    $exists = Topic::where('slug', $slug)
                        ->where('user_id', $user->id)
                        ->exists();

                    if ($exists) {
                        $fail(__('onboarding.topic_already_exists'));
                    }
                },
            ],
        ]);

        $topic = Topic::create([
            'user_id' => $user->id,
            'slug' => $slug,
            'is_system' => false,
            'is_approved' => false,
        ]);

        $user->notify(new SlugProcessingNotification($topic));

        session()->flash('message', __('onboarding.topic_processing'));

        $this->topic_name = '';

        // Dispatch event to refresh parent component
        $this->dispatch('topic-created');
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.onboarding.create-topic');
    }
}
