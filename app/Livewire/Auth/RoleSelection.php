<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Enums\System\SystemLanguageEnum;
use App\Enums\System\UserRoleEnum;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RoleSelection extends Component
{
    public bool $is_teacher = false;

    public bool $is_student = true;

    /** @var array<string> */
    public array $teaching_languages = [];

    /** @var array<string> */
    public array $learning_languages = [];

    /** @var SystemLanguageEnum[] */
    public array $languages = [];

    public function mount(): void
    {
        // Check if registration data exists in session
        if (! session()->has('registration_data') || ! session()->has('registration_native_lang')) {
            redirect()->route('register');

            return;
        }

        $this->languages = SystemLanguageEnum::all();

        // Set default learning language (opposite of native)
        $native_lang = session()->get('registration_native_lang');
        $default_learning = $native_lang === SystemLanguageEnum::ENGLISH->value
            ? SystemLanguageEnum::VIETNAMESE->value
            : SystemLanguageEnum::ENGLISH->value;
        $this->learning_languages = [$default_learning];
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [];

        // At least one role must be selected
        if (! $this->is_teacher && ! $this->is_student) {
            $rules['is_teacher'] = 'accepted';
        }

        // If teacher, at least one teaching language required
        if ($this->is_teacher) {
            $rules['teaching_languages'] = 'required|array|min:1';
            $rules['teaching_languages.*'] = 'required|string|in:'.implode(',', SystemLanguageEnum::values());
        }

        // If student, at least one learning language required
        if ($this->is_student) {
            $rules['learning_languages'] = 'required|array|min:1';
            $rules['learning_languages.*'] = 'required|string|in:'.implode(',', SystemLanguageEnum::values());
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'is_teacher.accepted' => __('auth.role_selection.at_least_one_role'),
            'teaching_languages.required' => __('auth.role_selection.teaching_lang_required'),
            'teaching_languages.min' => __('auth.role_selection.teaching_lang_required'),
            'learning_languages.required' => __('auth.role_selection.learning_lang_required'),
            'learning_languages.min' => __('auth.role_selection.learning_lang_required'),
        ];
    }

    public function completeRegistration(): void
    {
        // Validate at least one role
        if (! $this->is_teacher && ! $this->is_student) {
            $this->addError('roles', __('auth.role_selection.at_least_one_role'));

            return;
        }

        $this->validate();

        $registration_data = session()->get('registration_data');
        $native_lang = session()->get('registration_native_lang');

        if (! $registration_data || ! $native_lang) {
            redirect()->route('register');

            return;
        }

        // Create user
        $user = User::create([
            'first_name' => $registration_data['first_name'],
            'last_name' => $registration_data['last_name'],
            'email' => $registration_data['email'],
            'password' => $registration_data['password'],
            'date_of_birth' => $registration_data['date_of_birth'],
            'timezone' => $registration_data['timezone'] ?? 'UTC',
            'native_lang' => $native_lang,
            'facebook_id' => $registration_data['facebook_id'] ?? null,
            'zalo_user_id' => $registration_data['zalo_user_id'] ?? null,
            'avatar' => $registration_data['avatar'] ?? null,
            'email_verified_at' => $registration_data['email_verified_at'] ?? null,
        ]);

        // Assign roles
        if ($this->is_teacher) {
            $user->assignRole(UserRoleEnum::TEACHER);

            // Add teaching languages
            foreach ($this->teaching_languages as $language) {
                $lang_enum = SystemLanguageEnum::tryFrom($language);
                if ($lang_enum) {
                    $user->addTeachingLanguage($lang_enum);
                }
            }
        }

        if ($this->is_student) {
            $user->assignRole(UserRoleEnum::STUDENT);

            // Add learning languages
            foreach ($this->learning_languages as $language) {
                $lang_enum = SystemLanguageEnum::tryFrom($language);
                if ($lang_enum) {
                    $user->addLearningLanguage($lang_enum);
                }
            }
        }

        // Login user
        Auth::login($user);
        session()->regenerate();

        // Set a persistent cookie to indicate user has an account
        cookie()->queue('has_account', '1', 525600); // 1 year

        // Clear registration data from session
        session()->forget(['registration_data', 'registration_native_lang']);

        // Redirect to dashboard
        redirect()->route('dashboard');
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.auth.role-selection');
    }
}
