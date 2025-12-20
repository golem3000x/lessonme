<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\System\SystemLanguageEnum;
use App\Livewire\Auth\LanguageSelection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LanguageSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        session()->put('registration_data', ['email' => 'test@example.com']);
    }

    public function test_component_renders_successfully(): void
    {
        Livewire::test(LanguageSelection::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.auth.language-selection');
    }

    public function test_mount_redirects_without_registration_data(): void
    {
        session()->forget('registration_data');

        Livewire::test(LanguageSelection::class)
            ->assertRedirect(route('register'));
    }

    public function test_mount_loads_languages(): void
    {
        $component = Livewire::test(LanguageSelection::class);

        $languages = $component->get('languages');

        $this->assertNotEmpty($languages);
        $this->assertContainsOnlyInstancesOf(SystemLanguageEnum::class, $languages);
    }

    public function test_mount_sets_default_language_english_for_en_locale(): void
    {
        app()->setLocale('en');

        Livewire::test(LanguageSelection::class)
            ->assertSet('native_lang', SystemLanguageEnum::ENGLISH->value);
    }

    public function test_mount_sets_default_language_vietnamese_for_vi_locale(): void
    {
        app()->setLocale('vi');

        Livewire::test(LanguageSelection::class)
            ->assertSet('native_lang', SystemLanguageEnum::VIETNAMESE->value);
    }

    public function test_proceed_to_role_selection_validates_native_lang(): void
    {
        Livewire::test(LanguageSelection::class)
            ->set('native_lang', '')
            ->call('proceedToRoleSelection')
            ->assertHasErrors(['native_lang' => 'required']);
    }

    public function test_proceed_to_role_selection_validates_native_lang_in_enum(): void
    {
        Livewire::test(LanguageSelection::class)
            ->set('native_lang', 'invalid_lang')
            ->call('proceedToRoleSelection')
            ->assertHasErrors(['native_lang' => 'in']);
    }

    public function test_proceed_to_role_selection_saves_to_session_and_redirects(): void
    {
        Livewire::test(LanguageSelection::class)
            ->set('native_lang', SystemLanguageEnum::VIETNAMESE->value)
            ->call('proceedToRoleSelection')
            ->assertRedirect(route('auth.role'));

        $this->assertEquals(
            SystemLanguageEnum::VIETNAMESE->value,
            session('registration_native_lang')
        );
    }

}
