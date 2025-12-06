<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\System\SystemLanguageEnum;
use App\Livewire\Auth\LanguageSelection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class LanguageSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    private function setRegistrationData(array $data = []): array
    {
        $default_data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'timezone' => 'UTC',
        ];

        $registration_data = array_merge($default_data, $data);
        session()->put('registration_data', $registration_data);

        return $registration_data;
    }

    public function test_component_renders_successfully_with_session_data(): void
    {
        $this->setRegistrationData();

        Livewire::test(LanguageSelection::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.auth.language-selection');
    }

    public function test_mount_loads_system_languages(): void
    {
        $this->setRegistrationData();

        Livewire::test(LanguageSelection::class)
            ->assertSet('languages', function ($languages) {
                return is_array($languages) && count($languages) > 0;
            });
    }

    public function test_mount_sets_default_native_lang_to_english_when_locale_is_en(): void
    {
        $this->setRegistrationData();
        app()->setLocale('en');

        Livewire::test(LanguageSelection::class)
            ->assertSet('native_lang', SystemLanguageEnum::ENGLISH->value);
    }

    public function test_mount_sets_default_native_lang_to_vietnamese_when_locale_is_vi(): void
    {
        $this->setRegistrationData();
        app()->setLocale('vi');

        Livewire::test(LanguageSelection::class)
            ->assertSet('native_lang', SystemLanguageEnum::VIETNAMESE->value);
    }

    public function test_complete_registration_creates_user(): void
    {
        $this->setRegistrationData();

        Livewire::test(LanguageSelection::class)
            ->set('native_lang', SystemLanguageEnum::ENGLISH->value)
            ->call('completeRegistration');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'native_lang' => SystemLanguageEnum::ENGLISH->value,
        ]);
    }

    public function test_complete_registration_sets_learning_lang_opposite_to_native(): void
    {
        $this->setRegistrationData();

        Livewire::test(LanguageSelection::class)
            ->set('native_lang', SystemLanguageEnum::ENGLISH->value)
            ->call('completeRegistration');

        $user = User::where('email', '=', 'test@example.com')->first();
        $learning_languages = $user->getLearningLanguageValues();
        $this->assertContains(SystemLanguageEnum::VIETNAMESE->value, $learning_languages);
    }

    public function test_complete_registration_with_vietnamese_native_sets_english_learning(): void
    {
        $this->setRegistrationData();

        Livewire::test(LanguageSelection::class)
            ->set('native_lang', SystemLanguageEnum::VIETNAMESE->value)
            ->call('completeRegistration');

        $user = User::where('email', '=', 'test@example.com')->first();
        $learning_languages = $user->getLearningLanguageValues();
        $this->assertContains(SystemLanguageEnum::ENGLISH->value, $learning_languages);
    }

    public function test_complete_registration_validates_required_native_lang(): void
    {
        $this->setRegistrationData();

        Livewire::test(LanguageSelection::class)
            ->set('native_lang', '')
            ->call('completeRegistration')
            ->assertHasErrors(['native_lang' => 'required']);
    }

    public function test_complete_registration_validates_native_lang_is_valid_system_language(): void
    {
        $this->setRegistrationData();

        Livewire::test(LanguageSelection::class)
            ->set('native_lang', 'invalid-language')
            ->call('completeRegistration')
            ->assertHasErrors(['native_lang' => 'in']);
    }

    public function test_complete_registration_includes_facebook_id_if_present(): void
    {
        $this->setRegistrationData(['facebook_id' => 'fb123456']);

        Livewire::test(LanguageSelection::class)
            ->set('native_lang', SystemLanguageEnum::ENGLISH->value)
            ->call('completeRegistration');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'facebook_id' => 'fb123456',
        ]);
    }

    public function test_complete_registration_includes_zalo_user_id_if_present(): void
    {
        $this->setRegistrationData(['zalo_user_id' => 'zalo123456']);

        Livewire::test(LanguageSelection::class)
            ->set('native_lang', SystemLanguageEnum::ENGLISH->value)
            ->call('completeRegistration');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'zalo_user_id' => 'zalo123456',
        ]);
    }

    public function test_complete_registration_includes_avatar_if_present(): void
    {
        $this->setRegistrationData(['avatar' => 'avatars/test.jpg']);

        Livewire::test(LanguageSelection::class)
            ->set('native_lang', SystemLanguageEnum::ENGLISH->value)
            ->call('completeRegistration');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'avatar' => 'avatars/test.jpg',
        ]);
    }

    public function test_complete_registration_includes_email_verified_at_if_present(): void
    {
        $verified_at = now();
        $this->setRegistrationData(['email_verified_at' => $verified_at]);

        Livewire::test(LanguageSelection::class)
            ->set('native_lang', SystemLanguageEnum::ENGLISH->value)
            ->call('completeRegistration');

        $user = User::where('email', '=', 'test@example.com')->first();
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_complete_registration_uses_timezone_from_session(): void
    {
        $this->setRegistrationData(['timezone' => 'America/New_York']);

        Livewire::test(LanguageSelection::class)
            ->set('native_lang', SystemLanguageEnum::ENGLISH->value)
            ->call('completeRegistration');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'timezone' => 'America/New_York',
        ]);
    }

    public function test_complete_registration_defaults_timezone_to_utc(): void
    {
        $data = $this->setRegistrationData();
        unset($data['timezone']);
        session()->put('registration_data', $data);

        Livewire::test(LanguageSelection::class)
            ->set('native_lang', SystemLanguageEnum::ENGLISH->value)
            ->call('completeRegistration');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'timezone' => 'UTC',
        ]);
    }

    public function test_complete_registration_clears_session_data(): void
    {
        $this->setRegistrationData();

        Livewire::test(LanguageSelection::class)
            ->set('native_lang', SystemLanguageEnum::ENGLISH->value)
            ->call('completeRegistration');

        $this->assertFalse(session()->has('registration_data'));
    }

    public function test_complete_registration_authenticates_user(): void
    {
        $this->setRegistrationData();

        Livewire::test(LanguageSelection::class)
            ->set('native_lang', SystemLanguageEnum::ENGLISH->value)
            ->call('completeRegistration');

        $this->assertAuthenticated();
    }

    public function test_component_initializes_with_empty_native_lang(): void
    {
        $this->setRegistrationData();

        // Override locale to test default behavior
        app()->setLocale('unknown');

        Livewire::test(LanguageSelection::class)
            ->assertSet('native_lang', SystemLanguageEnum::ENGLISH->value);
    }
}
