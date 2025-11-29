<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\ChatSessionStatusEnum;
use App\Events\MessageSent;
use App\Livewire\Chat\ChatPanel;
use App\Models\Support\ChatSession;
use App\Models\Support\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class ChatPanelTest extends TestCase
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
            ->test(ChatPanel::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.chat.chat-panel');
    }

    public function test_mount_creates_chat_session_if_not_exists(): void
    {
        $this->assertDatabaseMissing('chat_sessions', [
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ChatPanel::class);

        $this->assertDatabaseHas('chat_sessions', [
            'user_id' => $this->user->id,
            'status' => ChatSessionStatusEnum::OPEN->value,
        ]);
    }

    public function test_mount_loads_existing_chat_session(): void
    {
        $chat_session = ChatSession::factory()->create([
            'user_id' => $this->user->id,
            'status' => ChatSessionStatusEnum::OPEN,
        ]);

        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->assertSet('chat_session.id', $chat_session->id);
    }

    public function test_mount_loads_recent_messages(): void
    {
        $chat_session = ChatSession::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Message::factory()->count(3)->create([
            'chat_session_id' => $chat_session->id,
            'user_id' => $this->user->id,
            'is_from_admin' => false,
        ]);

        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->assertSet('messages', function ($messages) {
                return count($messages) === 3;
            });
    }

    public function test_toggle_chat_opens_panel(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->assertSet('is_open', false)
            ->dispatch('chat-toggled', is_open: true)
            ->assertSet('is_open', true);
    }

    public function test_toggle_chat_closes_panel(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->set('is_open', true)
            ->dispatch('chat-toggled', is_open: false)
            ->assertSet('is_open', false);
    }

    public function test_close_chat_dispatches_event(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->set('is_open', true)
            ->call('closeChat')
            ->assertSet('is_open', false)
            ->assertDispatched('chat-toggled', is_open: false);
    }

    public function test_send_message_validates_required_field(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->set('new_message', '')
            ->call('sendMessage')
            ->assertHasErrors(['new_message' => 'required']);
    }

    public function test_send_message_validates_max_length(): void
    {
        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->set('new_message', str_repeat('a', 1001))
            ->call('sendMessage')
            ->assertHasErrors(['new_message' => 'max']);
    }

    public function test_send_message_creates_message_in_database(): void
    {
        Event::fake();
        Queue::fake();

        $chat_session = ChatSession::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->set('new_message', 'Hello, this is a test message')
            ->call('sendMessage');

        $this->assertDatabaseHas('messages', [
            'chat_session_id' => $chat_session->id,
            'user_id' => $this->user->id,
            'message' => 'Hello, this is a test message',
            'is_from_admin' => false,
            'is_read' => false,
        ]);
    }

    public function test_send_message_updates_chat_session_timestamp(): void
    {
        Event::fake();
        Queue::fake();

        $chat_session = ChatSession::factory()->create([
            'user_id' => $this->user->id,
            'last_message_at' => now()->subHour(),
        ]);

        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->set('new_message', 'Test message')
            ->call('sendMessage');

        $chat_session->refresh();

        $this->assertTrue($chat_session->last_message_at->isAfter(now()->subMinute()));
    }

    public function test_send_message_broadcasts_event(): void
    {
        Event::fake();

        ChatSession::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->set('new_message', 'Test message')
            ->call('sendMessage');

        Event::assertDispatched(MessageSent::class);
    }

    public function test_send_message_clears_input_field(): void
    {
        Event::fake();
        Queue::fake();

        ChatSession::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->set('new_message', 'Test message')
            ->call('sendMessage')
            ->assertSet('new_message', '');
    }

    public function test_send_message_adds_message_to_array(): void
    {
        Event::fake();
        Queue::fake();

        ChatSession::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->set('new_message', 'Test message')
            ->call('sendMessage')
            ->assertSet('messages', function ($messages) {
                return count($messages) === 1 && $messages[0]['message'] === 'Test message';
            });
    }

    public function test_send_message_dispatches_message_sent_event(): void
    {
        Event::fake();
        Queue::fake();

        ChatSession::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->set('new_message', 'Test message')
            ->call('sendMessage')
            ->assertDispatched('message-sent');
    }

    public function test_add_message_appends_new_message(): void
    {
        $chat_session = ChatSession::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $message = Message::factory()->create([
            'chat_session_id' => $chat_session->id,
            'user_id' => $this->user->id,
            'message' => 'New incoming message',
            'is_from_admin' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->dispatch('message-received', message_data: ['id' => $message->id])
            ->assertSet('messages', function ($messages) use ($message) {
                return count($messages) === 1 && $messages[0]['id'] === $message->id;
            });
    }

    public function test_add_message_prevents_duplicates(): void
    {
        $chat_session = ChatSession::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $message = Message::factory()->create([
            'chat_session_id' => $chat_session->id,
            'user_id' => $this->user->id,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(ChatPanel::class);

        // Add message first time
        $component->dispatch('message-received', message_data: ['id' => $message->id]);

        // Try to add same message again
        $component->dispatch('message-received', message_data: ['id' => $message->id])
            ->assertSet('messages', function ($messages) {
                return count($messages) === 1; // Should still be 1
            });
    }

    public function test_add_message_marks_as_read_when_chat_is_open(): void
    {
        $chat_session = ChatSession::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Create admin message first, then mark it as unread
        $message = Message::factory()->fromAdmin()->create([
            'chat_session_id' => $chat_session->id,
            'user_id' => $this->user->id,
            'is_read' => false,
        ]);

        // Open the chat first, then dispatch message
        $component = Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->set('is_open', true);

        // Load messages first (to ensure chat_session is loaded)
        $component->call('toggleChat', is_open: true);

        // Now dispatch the message-received event
        $component->dispatch('message-received', message_data: ['id' => $message->id]);

        $message->refresh();
        $this->assertTrue($message->is_read);
    }

    public function test_mark_messages_as_read_updates_unread_admin_messages(): void
    {
        $chat_session = ChatSession::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Message::factory()->count(3)->create([
            'chat_session_id' => $chat_session->id,
            'user_id' => $this->user->id,
            'is_read' => false,
            'is_from_admin' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->dispatch('chat-toggled', is_open: true);

        $this->assertEquals(0, Message::where('is_read', '=', false)->count());
    }

    public function test_mark_messages_as_read_does_not_mark_user_messages(): void
    {
        $chat_session = ChatSession::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Message::factory()->create([
            'chat_session_id' => $chat_session->id,
            'user_id' => $this->user->id,
            'is_read' => false,
            'is_from_admin' => false, // User message
        ]);

        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->dispatch('chat-toggled', is_open: true);

        // User messages should remain unread
        $this->assertEquals(1, Message::where('is_read', '=', false)->count());
    }

    public function test_mark_messages_as_read_resets_unread_count(): void
    {
        $chat_session = ChatSession::factory()->create([
            'user_id' => $this->user->id,
            'unread_count' => 5,
        ]);

        Message::factory()->count(5)->create([
            'chat_session_id' => $chat_session->id,
            'user_id' => $this->user->id,
            'is_read' => false,
            'is_from_admin' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->dispatch('chat-toggled', is_open: true);

        $chat_session->refresh();
        $this->assertEquals(0, $chat_session->unread_count);
    }

    public function test_component_loads_maximum_50_messages(): void
    {
        $chat_session = ChatSession::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Message::factory()->count(100)->create([
            'chat_session_id' => $chat_session->id,
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ChatPanel::class)
            ->assertSet('messages', function ($messages) {
                return count($messages) === 50;
            });
    }

    public function test_component_returns_early_if_user_not_authenticated(): void
    {
        Livewire::test(ChatPanel::class)
            ->assertSet('chat_session', null)
            ->assertSet('messages', []);
    }
}
