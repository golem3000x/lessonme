<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\System\UserRoleEnum;
use App\Livewire\Messages\MessagesIndex;
use App\Models\User\User;
use App\Models\User\UserMessage;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MessagesIndexTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $other_user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole(UserRoleEnum::STUDENT);

        $this->other_user = User::factory()->create();
        $this->other_user->assignRole(UserRoleEnum::TEACHER);
    }

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->user)
            ->test(MessagesIndex::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.messages.messages-index');
    }

    public function test_conversations_empty_initially(): void
    {
        Livewire::actingAs($this->user)
            ->test(MessagesIndex::class)
            ->assertSet('conversations', []);
    }

    public function test_loads_sent_conversations(): void
    {
        UserMessage::create([
            'sender_id' => $this->user->id,
            'recipient_id' => $this->other_user->id,
            'content' => 'Hello!',
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(MessagesIndex::class);

        $conversations = $component->get('conversations');
        $this->assertCount(1, $conversations);
        $this->assertEquals($this->other_user->id, $conversations[0]['user_id']);
    }

    public function test_loads_received_conversations(): void
    {
        UserMessage::create([
            'sender_id' => $this->other_user->id,
            'recipient_id' => $this->user->id,
            'content' => 'Hello!',
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(MessagesIndex::class);

        $conversations = $component->get('conversations');
        $this->assertCount(1, $conversations);
        $this->assertEquals($this->other_user->id, $conversations[0]['user_id']);
    }

    public function test_groups_messages_by_user(): void
    {
        UserMessage::create([
            'sender_id' => $this->user->id,
            'recipient_id' => $this->other_user->id,
            'content' => 'First message',
        ]);

        UserMessage::create([
            'sender_id' => $this->other_user->id,
            'recipient_id' => $this->user->id,
            'content' => 'Reply',
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(MessagesIndex::class);

        $conversations = $component->get('conversations');
        $this->assertCount(1, $conversations);
    }

    public function test_shows_last_message(): void
    {
        $first = UserMessage::create([
            'sender_id' => $this->user->id,
            'recipient_id' => $this->other_user->id,
            'content' => 'First message',
        ]);
        $first->created_at = now()->subMinutes(5);
        $first->save();

        $last = UserMessage::create([
            'sender_id' => $this->other_user->id,
            'recipient_id' => $this->user->id,
            'content' => 'Last message',
        ]);
        $last->created_at = now();
        $last->save();

        $component = Livewire::actingAs($this->user)
            ->test(MessagesIndex::class);

        $conversations = $component->get('conversations');
        $this->assertEquals('Last message', $conversations[0]['last_message']);
    }

    public function test_counts_unread_messages(): void
    {
        UserMessage::create([
            'sender_id' => $this->other_user->id,
            'recipient_id' => $this->user->id,
            'content' => 'Unread 1',
            'read_at' => null,
        ]);

        UserMessage::create([
            'sender_id' => $this->other_user->id,
            'recipient_id' => $this->user->id,
            'content' => 'Unread 2',
            'read_at' => null,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(MessagesIndex::class);

        $conversations = $component->get('conversations');
        $this->assertEquals(2, $conversations[0]['unread_count']);
    }

    public function test_read_messages_not_counted(): void
    {
        UserMessage::create([
            'sender_id' => $this->other_user->id,
            'recipient_id' => $this->user->id,
            'content' => 'Read message',
            'read_at' => now(),
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(MessagesIndex::class);

        $conversations = $component->get('conversations');
        $this->assertEquals(0, $conversations[0]['unread_count']);
    }

    public function test_multiple_conversations(): void
    {
        $third_user = User::factory()->create();

        UserMessage::create([
            'sender_id' => $this->user->id,
            'recipient_id' => $this->other_user->id,
            'content' => 'To other user',
        ]);

        UserMessage::create([
            'sender_id' => $this->user->id,
            'recipient_id' => $third_user->id,
            'content' => 'To third user',
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(MessagesIndex::class);

        $conversations = $component->get('conversations');
        $this->assertCount(2, $conversations);
    }

    public function test_conversation_includes_user_data(): void
    {
        UserMessage::create([
            'sender_id' => $this->user->id,
            'recipient_id' => $this->other_user->id,
            'content' => 'Hello!',
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(MessagesIndex::class);

        $conversations = $component->get('conversations');
        $this->assertArrayHasKey('user', $conversations[0]);
        $this->assertArrayHasKey('last_message_at', $conversations[0]);
    }
}
