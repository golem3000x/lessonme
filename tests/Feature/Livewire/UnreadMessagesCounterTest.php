<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\System\UserRoleEnum;
use App\Livewire\Messages\UnreadMessagesCounter;
use App\Models\User\User;
use App\Models\User\UserMessage;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UnreadMessagesCounterTest extends TestCase
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
            ->test(UnreadMessagesCounter::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.messages.unread-messages-counter');
    }

    public function test_unread_count_zero_initially(): void
    {
        Livewire::actingAs($this->user)
            ->test(UnreadMessagesCounter::class)
            ->assertSet('unread_count', 0);
    }

    public function test_counts_unread_messages(): void
    {
        UserMessage::create([
            'sender_id' => $this->other_user->id,
            'recipient_id' => $this->user->id,
            'content' => 'Unread message',
            'read_at' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(UnreadMessagesCounter::class)
            ->assertSet('unread_count', 1);
    }

    public function test_does_not_count_read_messages(): void
    {
        UserMessage::create([
            'sender_id' => $this->other_user->id,
            'recipient_id' => $this->user->id,
            'content' => 'Read message',
            'read_at' => now(),
        ]);

        Livewire::actingAs($this->user)
            ->test(UnreadMessagesCounter::class)
            ->assertSet('unread_count', 0);
    }

    public function test_does_not_count_sent_messages(): void
    {
        UserMessage::create([
            'sender_id' => $this->user->id,
            'recipient_id' => $this->other_user->id,
            'content' => 'Sent message',
            'read_at' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(UnreadMessagesCounter::class)
            ->assertSet('unread_count', 0);
    }

    public function test_counts_multiple_unread_messages(): void
    {
        UserMessage::create([
            'sender_id' => $this->other_user->id,
            'recipient_id' => $this->user->id,
            'content' => 'Message 1',
            'read_at' => null,
        ]);

        UserMessage::create([
            'sender_id' => $this->other_user->id,
            'recipient_id' => $this->user->id,
            'content' => 'Message 2',
            'read_at' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(UnreadMessagesCounter::class)
            ->assertSet('unread_count', 2);
    }

    public function test_on_messages_read_reloads_count(): void
    {
        $message = UserMessage::create([
            'sender_id' => $this->other_user->id,
            'recipient_id' => $this->user->id,
            'content' => 'Message',
            'read_at' => null,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(UnreadMessagesCounter::class)
            ->assertSet('unread_count', 1);

        $message->update(['read_at' => now()]);

        $component->dispatch('messages-read')
            ->assertSet('unread_count', 0);
    }
}
