<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Messages\UnreadMessagesBadge;
use App\Models\User\User;
use App\Models\User\UserMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UnreadMessagesBadgeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $sender;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->sender = User::factory()->create();
    }

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->user)
            ->test(UnreadMessagesBadge::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.messages.unread-messages-badge');
    }

    public function test_mount_initializes_unread_count_to_zero(): void
    {
        Livewire::actingAs($this->user)
            ->test(UnreadMessagesBadge::class)
            ->assertSet('unread_count', 0);
    }

    public function test_mount_counts_unread_messages(): void
    {
        for ($i = 0; $i < 3; $i++) {
            UserMessage::create([
                'sender_id' => $this->sender->id,
                'recipient_id' => $this->user->id,
                'content' => 'Test message '.$i,
                'read_at' => null,
            ]);
        }

        Livewire::actingAs($this->user)
            ->test(UnreadMessagesBadge::class)
            ->assertSet('unread_count', 3);
    }

    public function test_does_not_count_read_messages(): void
    {
        for ($i = 0; $i < 2; $i++) {
            UserMessage::create([
                'sender_id' => $this->sender->id,
                'recipient_id' => $this->user->id,
                'content' => 'Read message '.$i,
                'read_at' => now(),
            ]);
        }

        UserMessage::create([
            'sender_id' => $this->sender->id,
            'recipient_id' => $this->user->id,
            'content' => 'Unread message',
            'read_at' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(UnreadMessagesBadge::class)
            ->assertSet('unread_count', 1);
    }

    public function test_does_not_count_messages_to_other_users(): void
    {
        $other_user = User::factory()->create();

        for ($i = 0; $i < 2; $i++) {
            UserMessage::create([
                'sender_id' => $this->sender->id,
                'recipient_id' => $other_user->id,
                'content' => 'Message to other '.$i,
                'read_at' => null,
            ]);
        }

        Livewire::actingAs($this->user)
            ->test(UnreadMessagesBadge::class)
            ->assertSet('unread_count', 0);
    }

    public function test_dm_received_event_updates_count(): void
    {
        UserMessage::create([
            'sender_id' => $this->sender->id,
            'recipient_id' => $this->user->id,
            'content' => 'First message',
            'read_at' => null,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(UnreadMessagesBadge::class)
            ->assertSet('unread_count', 1);

        // Add another message
        UserMessage::create([
            'sender_id' => $this->sender->id,
            'recipient_id' => $this->user->id,
            'content' => 'Second message',
            'read_at' => null,
        ]);

        $component->dispatch('dm-received',
            id: 1,
            sender_id: $this->sender->id,
            recipient_id: $this->user->id,
            content: 'Test',
            read_at: null,
            created_at: now()->toISOString(),
            sender_name: 'Test',
            sender_avatar: null
        )->assertSet('unread_count', 2);
    }

    public function test_messages_read_event_updates_count(): void
    {
        $message = UserMessage::create([
            'sender_id' => $this->sender->id,
            'recipient_id' => $this->user->id,
            'content' => 'Test message',
            'read_at' => null,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(UnreadMessagesBadge::class)
            ->assertSet('unread_count', 1);

        // Mark as read
        $message->update(['read_at' => now()]);

        $component->dispatch('messages-read')
            ->assertSet('unread_count', 0);
    }

    public function test_guest_user_has_zero_count(): void
    {
        Livewire::test(UnreadMessagesBadge::class)
            ->assertSet('unread_count', 0);
    }
}
