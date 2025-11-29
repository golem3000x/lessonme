<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Help\QuickTourBanner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuickTourBannerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'first_login_completed_at' => null,
        ]);
    }

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->user)
            ->test(QuickTourBanner::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.help.quick-tour-banner');
    }

    public function test_mount_shows_banner_for_new_user(): void
    {
        Livewire::actingAs($this->user)
            ->test(QuickTourBanner::class)
            ->assertSet('show_banner', true);
    }

    public function test_mount_hides_banner_for_user_with_completed_first_login(): void
    {
        $experienced_user = User::factory()->create([
            'first_login_completed_at' => now(),
        ]);

        Livewire::actingAs($experienced_user)
            ->test(QuickTourBanner::class)
            ->assertSet('show_banner', false);
    }

    public function test_start_tour_updates_first_login_completed_at(): void
    {
        Livewire::actingAs($this->user)
            ->test(QuickTourBanner::class)
            ->call('startTour');

        $this->user->refresh();
        $this->assertNotNull($this->user->first_login_completed_at);
    }

    public function test_start_tour_hides_banner(): void
    {
        Livewire::actingAs($this->user)
            ->test(QuickTourBanner::class)
            ->call('startTour')
            ->assertSet('show_banner', false);
    }

    public function test_start_tour_dispatches_shepherd_tour_event(): void
    {
        Livewire::actingAs($this->user)
            ->test(QuickTourBanner::class)
            ->call('startTour')
            ->assertDispatched('start-shepherd-tour');
    }

    public function test_dismiss_temporarily_hides_banner(): void
    {
        Livewire::actingAs($this->user)
            ->test(QuickTourBanner::class)
            ->call('dismissTemporarily')
            ->assertSet('show_banner', false);
    }

    public function test_dismiss_temporarily_does_not_update_database(): void
    {
        Livewire::actingAs($this->user)
            ->test(QuickTourBanner::class)
            ->call('dismissTemporarily');

        $this->user->refresh();
        $this->assertNull($this->user->first_login_completed_at);
    }

    public function test_dismiss_permanently_updates_first_login_completed_at(): void
    {
        Livewire::actingAs($this->user)
            ->test(QuickTourBanner::class)
            ->call('dismissPermanently');

        $this->user->refresh();
        $this->assertNotNull($this->user->first_login_completed_at);
    }

    public function test_dismiss_permanently_hides_banner(): void
    {
        Livewire::actingAs($this->user)
            ->test(QuickTourBanner::class)
            ->call('dismissPermanently')
            ->assertSet('show_banner', false);
    }

    public function test_handle_tour_completed_updates_first_login_for_new_user(): void
    {
        Livewire::actingAs($this->user)
            ->test(QuickTourBanner::class)
            ->dispatch('tour-completed');

        $this->user->refresh();
        $this->assertNotNull($this->user->first_login_completed_at);
    }

    public function test_handle_tour_completed_hides_banner(): void
    {
        Livewire::actingAs($this->user)
            ->test(QuickTourBanner::class)
            ->dispatch('tour-completed')
            ->assertSet('show_banner', false);
    }

    public function test_handle_tour_completed_does_not_update_if_already_completed(): void
    {
        $completed_time = now()->subDay();
        $experienced_user = User::factory()->create([
            'first_login_completed_at' => $completed_time,
        ]);

        Livewire::actingAs($experienced_user)
            ->test(QuickTourBanner::class)
            ->dispatch('tour-completed');

        $experienced_user->refresh();
        $this->assertEquals($completed_time->timestamp, $experienced_user->first_login_completed_at->timestamp);
    }

    public function test_component_works_for_guest_user(): void
    {
        Livewire::test(QuickTourBanner::class)
            ->assertSet('show_banner', true);
    }

    public function test_start_tour_handles_guest_gracefully(): void
    {
        Livewire::test(QuickTourBanner::class)
            ->call('startTour')
            ->assertSet('show_banner', false);
    }

    public function test_dismiss_permanently_handles_guest_gracefully(): void
    {
        Livewire::test(QuickTourBanner::class)
            ->call('dismissPermanently')
            ->assertSet('show_banner', false);
    }

    public function test_handle_tour_completed_handles_guest_gracefully(): void
    {
        Livewire::test(QuickTourBanner::class)
            ->dispatch('tour-completed')
            ->assertSet('show_banner', false);
    }

    public function test_banner_shows_again_after_temporary_dismiss_on_remount(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(QuickTourBanner::class);

        $component->call('dismissTemporarily')
            ->assertSet('show_banner', false);

        // Remount component (simulating page reload)
        $new_component = Livewire::actingAs($this->user)
            ->test(QuickTourBanner::class);

        $new_component->assertSet('show_banner', true);
    }

    public function test_banner_stays_hidden_after_permanent_dismiss_on_remount(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(QuickTourBanner::class);

        $component->call('dismissPermanently')
            ->assertSet('show_banner', false);

        // Remount component (simulating page reload)
        $new_component = Livewire::actingAs($this->user->fresh())
            ->test(QuickTourBanner::class);

        $new_component->assertSet('show_banner', false);
    }

    public function test_multiple_dismiss_actions_are_idempotent(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(QuickTourBanner::class);

        $component->call('dismissPermanently');
        $component->call('dismissPermanently');
        $component->call('startTour');

        $this->user->refresh();
        $this->assertNotNull($this->user->first_login_completed_at);
    }
}
