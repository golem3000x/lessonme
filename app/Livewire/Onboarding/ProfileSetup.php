<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Enums\System\SystemLanguageEnum;
use App\Jobs\Moderate\ModerateKeywordJob;
use App\Models\Keyword\Keyword;
use App\Models\Keyword\KeywordTranslation;
use App\Models\Keyword\UserKeywordVisibility as UserKeywordVisibilityModel;
use App\Models\Topic\Topic;
use App\Models\Topic\UserTopicVisibility;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileSetup extends Component
{
    use WithFileUploads;

    public string $first_name = '';

    public string $last_name = '';

    public string $date_of_birth = '';

    public string $bio = '';

    public string $native_lang = '';

    /** @var array<string> */
    public array $learning_languages = [];

    public bool $is_looking_for_teacher = false;

    public bool $is_accepting_students = false;

    public bool $is_teacher = false;

    public bool $is_student = false;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $avatar = null;

    /** @var SystemLanguageEnum[] */
    public array $languages = [];

    /** @var array<int, string> */
    public array $new_keyword_by_topic = [];

    /** @var array<int> */
    public array $visible_keyword_ids = [];

    /** @var array<int, array<int, array{id: int, label: string}>> */
    public array $topic_keywords = [];

    /** @var array<int, array{id: int, label: string}> */
    public array $available_topics = [];

    /** @var array<int> */
    public array $visible_topic_ids = [];

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->first_name = $user->first_name ?? '';
        $this->last_name = $user->last_name ?? '';
        $this->date_of_birth = $user->date_of_birth?->format('Y-m-d') ?? '';
        $this->bio = $user->bio ?? '';
        $this->native_lang = $user->native_lang;
        $this->learning_languages = $user->getLearningLanguageValues();
        $this->is_looking_for_teacher = $user->is_looking_for_teacher ?? false;
        $this->is_accepting_students = $user->is_accepting_students ?? false;
        $this->is_teacher = $user->isTeacher();
        $this->is_student = $user->isStudent();

        $this->languages = SystemLanguageEnum::all();

        $this->loadTopicsAndKeywords();
    }

    protected function loadTopicsAndKeywords(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $topics = Topic::with(['translations', 'keywords.translations', 'visibility', 'keywords.visibility'])
            ->where('is_system', '=', true)
            ->get();

        $this->available_topics = $topics->map(function (Topic $topic): array {
            return [
                'id' => $topic->id,
                'label' => $topic->translatedValue(),
            ];
        })->toArray();

        $this->visible_topic_ids = $topics->filter(function (Topic $topic) use ($user): bool {
            $visibility = $topic->visibility()->where('user_id', '=', $user->id)->first();

            return ! $visibility || ! $visibility->is_hidden;
        })->pluck('id')->toArray();

        foreach ($topics as $topic) {
            $keywords = $topic->keywords;
            $this->topic_keywords[$topic->id] = $keywords->map(function (Keyword $keyword): array {
                return [
                    'id' => $keyword->id,
                    'label' => $keyword->translatedValue(),
                ];
            })->toArray();

            foreach ($keywords as $keyword) {
                $visibility = $keyword->visibility()->where('user_id', '=', $user->id)->first();
                if (! $visibility || ! $visibility->is_hidden) {
                    $this->visible_keyword_ids[] = $keyword->id;
                }
            }
        }
    }

    public function addKeyword(int $topic_id): void
    {
        $keyword_text = $this->new_keyword_by_topic[$topic_id] ?? '';

        if (empty(trim($keyword_text))) {
            return;
        }

        /** @var User $user */
        $user = Auth::user();

        $keyword = Keyword::create([
            'topic_id' => $topic_id,
            'slug' => Str::slug($keyword_text).'-'.uniqid(),
            'user_id' => $user->id,
            'is_system' => false,
            'is_approved' => false,
        ]);

        KeywordTranslation::create([
            'keyword_id' => $keyword->id,
            'language_code' => $this->native_lang,
            'value' => $keyword_text,
        ]);

        UserKeywordVisibilityModel::create([
            'user_id' => $user->id,
            'keyword_id' => $keyword->id,
            'is_hidden' => false,
        ]);

        $this->topic_keywords[$topic_id][] = [
            'id' => $keyword->id,
            'label' => $keyword_text,
        ];

        $this->visible_keyword_ids[] = $keyword->id;

        $this->new_keyword_by_topic[$topic_id] = '';

        dispatch(new ModerateKeywordJob($keyword));
    }

    /**
     * @return mixed
     */
    public function removeAvatar()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        session()->flash('message', __('onboarding.avatar_removed'));

        return redirect()->route('onboarding.profile');
    }

    /**
     * @return mixed
     */
    public function save()
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'bio' => 'nullable|string|max:500',
            'native_lang' => 'required|string',
            'avatar' => 'nullable|image|max:2048',
        ];

        // Learning languages required only for students
        if ($this->is_student) {
            $rules['learning_languages'] = 'required|array|min:1';
            $rules['learning_languages.*'] = 'required|string|in:'.implode(',', SystemLanguageEnum::values());
        }

        $this->validate($rules, [
            'native_lang.different' => __('onboarding.languages_must_be_different'),
            'learning_languages.required' => __('onboarding.learning_lang_required'),
            'learning_languages.min' => __('onboarding.learning_lang_required'),
            'avatar.image' => __('onboarding.avatar_must_be_image'),
            'avatar.max' => __('onboarding.avatar_max_size'),
        ]);

        /** @var User $user */
        $user = Auth::user();

        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'bio' => $this->bio ?: null,
            'native_lang' => $this->native_lang,
            'is_looking_for_teacher' => $this->is_student ? $this->is_looking_for_teacher : false,
            'is_accepting_students' => $this->is_teacher ? $this->is_accepting_students : false,
        ];

        // Handle avatar upload
        if ($this->avatar) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Generate unique filename and store
            $filename = uniqid('avatar_').'.'.$this->avatar->getClientOriginalExtension();
            $path = $this->avatar->storeAs('avatars', $filename, 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        // Sync learning languages for students
        if ($this->is_student) {
            $user->syncLearningLanguages($this->learning_languages);
        }

        // Refresh auth user data in session
        $user = $user->fresh();

        if (! $user) {
            return redirect()->route('login');
        }

        Auth::login($user);

        $this->saveTopicVisibility($user);
        $this->saveKeywordVisibility($user);

        session()->flash('message', __('onboarding.profile_saved'));

        // Redirect to dashboard
        return redirect()->route('dashboard');
    }

    protected function saveTopicVisibility(User $user): void
    {
        $topics = Topic::where('is_system', '=', true)->get();

        foreach ($topics as $topic) {
            $is_visible = in_array($topic->id, $this->visible_topic_ids, true);

            UserTopicVisibility::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'topic_id' => $topic->id,
                ],
                [
                    'is_hidden' => ! $is_visible,
                ]
            );
        }
    }

    protected function saveKeywordVisibility(User $user): void
    {
        $all_keyword_ids = [];

        foreach ($this->topic_keywords as $keywords) {
            foreach ($keywords as $keyword) {
                $all_keyword_ids[] = $keyword['id'];
            }
        }

        foreach ($all_keyword_ids as $keyword_id) {
            $is_visible = in_array($keyword_id, $this->visible_keyword_ids, true);

            UserKeywordVisibilityModel::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'keyword_id' => $keyword_id,
                ],
                [
                    'is_hidden' => ! $is_visible,
                ]
            );
        }
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.onboarding.profile-setup');
    }
}
