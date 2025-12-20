<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Messages\UnreadMessagesDropdownItem;
use App\Models\User\User;
use App\Models\User\UserMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UnreadMessagesDropdownItemTest extends TestCase
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
            ->test(UnreadMessagesDropdownItem::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.messages.unread-messages-dropdown-item');
    }

    public function test_mount_initializes_unread_count_to_zero(): void
    {
        Livewire::actingAs($this->user)
            ->test(UnreadMessagesDropdownItem::class)
            ->assertSet('unread_count', 0);
    }

    public function test_mount_counts_unread_messages(): void
    {
        for ($i = 0; $i < 5; $i++) {
            UserMessage::create([
                'sender_id' => $this->sender->id,
                'recipient_id' => $this->user->id,
                'content' => 'Test message '.$i,
                'read_at' => null,
            ]);
        }

        Livewire::actingAs($this->user)
            ->test(UnreadMessagesDropdownItem::class)
            ->assertSet('unread_count', 5);
    }

    public function test_does_not_count_read_messages(): void
    {
        for ($i = 0; $i < 3; $i++) {
            UserMessage::create([
                'sender_id' => $this->sender->id,
                'recipient_id' => $this->user->id,
                'content' => 'Read message '.$i,
                'read_at' => now(),
            ]);
        }

        Livewire::actingAs($this->user)
            ->test(UnreadMessagesDropdownItem::class)
            ->assertSet('unread_count', 0);
    }

    public function test_dm_received_event_updates_count(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(UnreadMessagesDropdownItem::class)
            ->assertSet('unread_count', 0);

        UserMessage::create([
            'sender_id' => $this->sender->id,
            'recipient_id' => $this->user->id,
            'content' => 'New message',
            'read_at' => null,
        ]);

        $component->dispatch('dm-received',
            id: 1,
            sender_id: $this->sender->id,
            recipient_id: $this->user->id,
            content: 'Hello',
            read_at: null,
            created_at: now()->toISOString(),
            sender_name: 'Sender',
            sender_avatar: null
        )->assertSet('unread_count', 1);
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
            ->test(UnreadMessagesDropdownItem::class)
            ->assertSet('unread_count', 1);

        $message->update(['read_at' => now()]);

        $component->dispatch('messages-read')
            ->assertSet('unread_count', 0);
    }

    public function test_guest_user_has_zero_count(): void
    {
        Livewire::test(UnreadMessagesDropdownItem::class)
            ->assertSet('unread_count', 0);
    }
}
