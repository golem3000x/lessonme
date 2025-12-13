<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Onboarding\LanguageSwitcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LanguageSwitcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders_successfully(): void
    {
        Livewire::test(LanguageSwitcher::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.onboarding.language-switcher');
    }

    public function test_mount_sets_current_locale_from_app(): void
    {
        app()->setLocale('en');

        Livewire::test(LanguageSwitcher::class)
            ->assertSet('current_locale', 'en');
    }

    public function test_mount_sets_target_locale_to_vi_when_current_is_en(): void
    {
        $this->markTestSkipped('target_locale property was removed from LanguageSwitcher component');
    }

    public function test_mount_sets_target_locale_to_en_when_current_is_vi(): void
    {
        $this->markTestSkipped('target_locale property was removed from LanguageSwitcher component');
    }

    public function test_component_initializes_with_current_locale(): void
    {
        app()->setLocale('vi');

        Livewire::test(LanguageSwitcher::class)
            ->assertSet('current_locale', 'vi');
    }

    public function test_target_locale_is_opposite_of_current(): void
    {
        app()->setLocale('en');

        $component = Livewire::test(LanguageSwitcher::class);

        $currentLocale = $component->get('current_locale');
        $targetLocale = $component->get('target_locale');

        $this->assertNotEquals($currentLocale, $targetLocale);
    }
}
