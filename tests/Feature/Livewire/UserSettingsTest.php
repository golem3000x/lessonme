<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\System\UserRoleEnum;
use App\Livewire\Settings\UserSettings;
use App\Models\User\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole(UserRoleEnum::STUDENT);
    }

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserSettings::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.settings.user-settings');
    }

    public function test_mount_loads_show_greeting_setting(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserSettings::class)
            ->assertSet('show_greeting', true);
    }

    public function test_mount_loads_false_show_greeting_setting(): void
    {
        $this->user->setSetting('show_greeting', false);

        Livewire::actingAs($this->user)
            ->test(UserSettings::class)
            ->assertSet('show_greeting', false);
    }

    public function test_updating_show_greeting_saves_setting(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserSettings::class)
            ->set('show_greeting', false);

        $this->user->refresh();
        $this->assertFalse($this->user->getSetting('show_greeting'));
    }

    public function test_toggle_show_greeting_on(): void
    {
        $this->user->setSetting('show_greeting', false);

        Livewire::actingAs($this->user)
            ->test(UserSettings::class)
            ->set('show_greeting', true);

        $this->user->refresh();
        $this->assertTrue($this->user->getSetting('show_greeting'));
    }
}
