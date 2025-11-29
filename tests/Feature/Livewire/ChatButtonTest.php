<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Chat\ChatButton;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChatButtonTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatButton::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.chat.chat-button');
    }

    public function test_component_initializes_with_closed_state(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatButton::class)
            ->assertSet('is_chat_open', false)
            ->assertSet('unread_count', 0);
    }

    public function test_toggle_chat_opens_chat(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatButton::class)
            ->assertSet('is_chat_open', false)
            ->call('toggleChat')
            ->assertSet('is_chat_open', true);
    }

    public function test_toggle_chat_closes_chat(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatButton::class)
            ->set('is_chat_open', true)
            ->call('toggleChat')
            ->assertSet('is_chat_open', false);
    }

    public function test_toggle_chat_dispatches_event_with_open_state(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatButton::class)
            ->call('toggleChat')
            ->assertDispatched('chat-toggled', is_open: true);
    }

    public function test_toggle_chat_dispatches_event_with_close_state(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatButton::class)
            ->set('is_chat_open', true)
            ->call('toggleChat')
            ->assertDispatched('chat-toggled', is_open: false);
    }

    public function test_toggle_chat_resets_unread_count_when_opening(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatButton::class)
            ->set('unread_count', 5)
            ->call('toggleChat')
            ->assertSet('unread_count', 0);
    }

    public function test_sync_chat_state_opens_chat(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatButton::class)
            ->assertSet('is_chat_open', false)
            ->dispatch('chat-toggled', is_open: true)
            ->assertSet('is_chat_open', true);
    }

    public function test_sync_chat_state_closes_chat(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatButton::class)
            ->set('is_chat_open', true)
            ->dispatch('chat-toggled', is_open: false)
            ->assertSet('is_chat_open', false);
    }

    public function test_sync_chat_state_resets_unread_count_when_opening(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatButton::class)
            ->set('unread_count', 10)
            ->dispatch('chat-toggled', is_open: true)
            ->assertSet('unread_count', 0);
    }

    public function test_sync_chat_state_preserves_unread_count_when_closing(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatButton::class)
            ->set('unread_count', 10)
            ->set('is_chat_open', true)
            ->dispatch('chat-toggled', is_open: false)
            ->assertSet('unread_count', 10);
    }

    public function test_multiple_toggle_calls_work_correctly(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ChatButton::class);

        // First toggle - open
        $component->call('toggleChat')
            ->assertSet('is_chat_open', true);

        // Second toggle - close
        $component->call('toggleChat')
            ->assertSet('is_chat_open', false);

        // Third toggle - open again
        $component->call('toggleChat')
            ->assertSet('is_chat_open', true);
    }

    public function test_component_can_handle_large_unread_count(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatButton::class)
            ->set('unread_count', 999)
            ->assertSet('unread_count', 999)
            ->call('toggleChat')
            ->assertSet('unread_count', 0);
    }

    public function test_component_synchronizes_with_external_chat_toggle(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ChatButton::class)
            ->assertSet('is_chat_open', false);

        // Simulate external event (e.g., from ChatPanel)
        $component->dispatch('chat-toggled', is_open: true)
            ->assertSet('is_chat_open', true);

        // Toggle from button
        $component->call('toggleChat')
            ->assertSet('is_chat_open', false);

        // Another external event
        $component->dispatch('chat-toggled', is_open: true)
            ->assertSet('is_chat_open', true);
    }
}
