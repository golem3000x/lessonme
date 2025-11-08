<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Events\SlugEntityChanged;
use App\Models\Keyword\Keyword;
use App\Models\User;
use App\Notifications\Processing\SlugEntityProcessingNotification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class CreateKeyword extends Component
{
    public int $topic_id;

    public string $keyword_value = '';

    public string $native_lang;

    public function mount(int $topicId): void
    {
        $this->topic_id = $topicId;

        /** @var User $user */
        $user = Auth::user();
        $this->native_lang = $user->native_lang;
    }

    public function saveKeyword(): void
    {
        $value = trim($this->keyword_value);
        if ($value === '') {
            return;
        }

        /** @var User $user */
        $user = Auth::user();

        $slug = Str::slug($value);

        // Validate uniqueness of keyword slug within topic
        $this->validate([
            'keyword_value' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($slug) {
                    // Check if keyword with same slug already exists in this topic
                    $exists = Keyword::where('slug', $slug)
                        ->where('topic_id', $this->topic_id)
                        ->exists();

                    if ($exists) {
                        $fail(__('onboarding.keyword_already_exists'));
                    }
                },
            ],
        ]);

        $keyword = Keyword::create([
            'user_id' => $user->id,
            'topic_id' => $this->topic_id,
            'slug' => $slug,
            'is_system' => false,
        ]);

        $user->notify(new SlugEntityProcessingNotification($keyword));

        session()->flash('message', __('onboarding.keyword_processing'));

        $this->keyword_value = '';

        // Dispatch event to refresh parent component
        $this->dispatch('keyword-created', topicId: $this->topic_id);
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.onboarding.create-keyword');
    }
}
