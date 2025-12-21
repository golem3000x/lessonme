<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\System\SystemLanguageEnum;
use App\Enums\System\UserRoleEnum;
use App\Livewire\Auth\RoleSelection;
use App\Models\User\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class RoleSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function setRegistrationSession(): void
    {
        session()->put('registration_data', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'date_of_birth' => '1990-01-01',
            'timezone' => 'UTC',
        ]);
        session()->put('registration_native_lang', SystemLanguageEnum::ENGLISH->value);
    }

    // ===== RENDER TESTS =====

    public function test_component_renders_successfully(): void
    {
        $this->setRegistrationSession();

        Livewire::test(RoleSelection::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.auth.role-selection');
    }

    public function test_redirects_without_registration_data(): void
    {
        Livewire::test(RoleSelection::class)
            ->assertRedirect(route('register'));
    }

    // ===== MOUNT TESTS =====

    public function test_mount_sets_default_learning_language(): void
    {
        $this->setRegistrationSession();

        Livewire::test(RoleSelection::class)
            ->assertSet('learning_languages', [SystemLanguageEnum::VIETNAMESE->value]);
    }

    public function test_mount_sets_vietnamese_default_for_english_native(): void
    {
        session()->put('registration_data', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'date_of_birth' => '1990-01-01',
        ]);
        session()->put('registration_native_lang', SystemLanguageEnum::ENGLISH->value);

        Livewire::test(RoleSelection::class)
            ->assertSet('learning_languages', [SystemLanguageEnum::VIETNAMESE->value]);
    }

    public function test_mount_sets_english_default_for_vietnamese_native(): void
    {
        session()->put('registration_data', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'date_of_birth' => '1990-01-01',
        ]);
        session()->put('registration_native_lang', SystemLanguageEnum::VIETNAMESE->value);

        Livewire::test(RoleSelection::class)
            ->assertSet('learning_languages', [SystemLanguageEnum::ENGLISH->value]);
    }

    public function test_mount_loads_languages(): void
    {
        $this->setRegistrationSession();

        $component = Livewire::test(RoleSelection::class);

        $languages = $component->get('languages');
        $this->assertNotEmpty($languages);
    }

    // ===== DEFAULT VALUES TESTS =====

    public function test_is_student_true_by_default(): void
    {
        $this->setRegistrationSession();

        Livewire::test(RoleSelection::class)
            ->assertSet('is_student', true);
    }

    public function test_is_teacher_false_by_default(): void
    {
        $this->setRegistrationSession();

        Livewire::test(RoleSelection::class)
            ->assertSet('is_teacher', false);
    }

    // ===== VALIDATION TESTS =====

    public function test_validation_requires_at_least_one_role(): void
    {
        $this->setRegistrationSession();

        Livewire::test(RoleSelection::class)
            ->set('is_student', false)
            ->set('is_teacher', false)
            ->call('completeRegistration')
            ->assertHasErrors(['roles']);
    }

    public function test_validation_requires_teaching_languages_for_teacher(): void
    {
        $this->setRegistrationSession();

        Livewire::test(RoleSelection::class)
            ->set('is_teacher', true)
            ->set('is_student', false)
            ->set('teaching_languages', [])
            ->call('completeRegistration')
            ->assertHasErrors(['teaching_languages']);
    }

    public function test_validation_requires_learning_languages_for_student(): void
    {
        $this->setRegistrationSession();

        Livewire::test(RoleSelection::class)
            ->set('is_student', true)
            ->set('learning_languages', [])
            ->call('completeRegistration')
            ->assertHasErrors(['learning_languages']);
    }

    // ===== COMPLETE REGISTRATION TESTS =====

    public function test_complete_registration_creates_student(): void
    {
        $this->setRegistrationSession();

        Livewire::test(RoleSelection::class)
            ->set('is_student', true)
            ->set('is_teacher', false)
            ->set('learning_languages', [SystemLanguageEnum::VIETNAMESE->value])
            ->call('completeRegistration');

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole(UserRoleEnum::STUDENT));
        $this->assertFalse($user->hasRole(UserRoleEnum::TEACHER));
    }

    public function test_complete_registration_creates_teacher(): void
    {
        $this->setRegistrationSession();

        Livewire::test(RoleSelection::class)
            ->set('is_student', false)
            ->set('is_teacher', true)
            ->set('teaching_languages', [SystemLanguageEnum::ENGLISH->value])
            ->call('completeRegistration');

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole(UserRoleEnum::TEACHER));
        $this->assertFalse($user->hasRole(UserRoleEnum::STUDENT));
    }

    public function test_complete_registration_creates_both_roles(): void
    {
        $this->setRegistrationSession();

        Livewire::test(RoleSelection::class)
            ->set('is_student', true)
            ->set('is_teacher', true)
            ->set('learning_languages', [SystemLanguageEnum::VIETNAMESE->value])
            ->set('teaching_languages', [SystemLanguageEnum::ENGLISH->value])
            ->call('completeRegistration');

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole(UserRoleEnum::STUDENT));
        $this->assertTrue($user->hasRole(UserRoleEnum::TEACHER));
    }

    public function test_complete_registration_adds_learning_languages(): void
    {
        $this->setRegistrationSession();

        Livewire::test(RoleSelection::class)
            ->set('is_student', true)
            ->set('learning_languages', [SystemLanguageEnum::VIETNAMESE->value, SystemLanguageEnum::ENGLISH->value])
            ->call('completeRegistration');

        $user = User::where('email', 'test@example.com')->first();

        $learning_langs = $user->getLearningLanguageValues();
        $this->assertContains(SystemLanguageEnum::VIETNAMESE->value, $learning_langs);
        $this->assertContains(SystemLanguageEnum::ENGLISH->value, $learning_langs);
    }

    public function test_complete_registration_adds_teaching_languages(): void
    {
        $this->setRegistrationSession();

        Livewire::test(RoleSelection::class)
            ->set('is_student', false)
            ->set('is_teacher', true)
            ->set('teaching_languages', [SystemLanguageEnum::ENGLISH->value])
            ->call('completeRegistration');

        $user = User::where('email', 'test@example.com')->first();

        $teaching_langs = $user->getTeachingLanguageValues();
        $this->assertContains(SystemLanguageEnum::ENGLISH->value, $teaching_langs);
    }

    public function test_complete_registration_clears_session(): void
    {
        $this->setRegistrationSession();

        Livewire::test(RoleSelection::class)
            ->set('is_student', true)
            ->set('learning_languages', [SystemLanguageEnum::VIETNAMESE->value])
            ->call('completeRegistration');

        $this->assertNull(session()->get('registration_data'));
        $this->assertNull(session()->get('registration_native_lang'));
    }

    public function test_complete_registration_redirects_to_dashboard(): void
    {
        $this->setRegistrationSession();

        Livewire::test(RoleSelection::class)
            ->set('is_student', true)
            ->set('learning_languages', [SystemLanguageEnum::VIETNAMESE->value])
            ->call('completeRegistration')
            ->assertRedirect(route('dashboard'));
    }

    public function test_complete_registration_logs_in_user(): void
    {
        $this->setRegistrationSession();

        Livewire::test(RoleSelection::class)
            ->set('is_student', true)
            ->set('learning_languages', [SystemLanguageEnum::VIETNAMESE->value])
            ->call('completeRegistration');

        $this->assertAuthenticated();
    }
}
