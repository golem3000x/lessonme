<?php

declare(strict_types=1);

namespace Database\Factories\Support;

use App\Enums\ChatSessionStatusEnum;
use App\Models\Support\ChatSession;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatSession>
 */
class ChatSessionFactory extends Factory
{
    protected $model = ChatSession::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'last_message_at' => fake()->optional()->dateTimeBetween('-1 week', 'now'),
            'unread_count' => fake()->numberBetween(0, 10),
            'status' => ChatSessionStatusEnum::OPEN,
        ];
    }

    /**
     * Indicate that the chat session is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ChatSessionStatusEnum::CLOSED,
        ]);
    }

    /**
     * Indicate that the chat session has unread messages.
     */
    public function withUnreadMessages(int $count = 5): static
    {
        return $this->state(fn (array $attributes) => [
            'unread_count' => $count,
        ]);
    }

    /**
     * Indicate that the chat session has no unread messages.
     */
    public function withoutUnreadMessages(): static
    {
        return $this->state(fn (array $attributes) => [
            'unread_count' => 0,
        ]);
    }

    /**
     * Indicate that the chat session has recent activity.
     */
    public function recentlyActive(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_message_at' => now()->subMinutes(fake()->numberBetween(1, 30)),
        ]);
    }

    /**
     * Indicate that the chat session has been inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_message_at' => now()->subDays(fake()->numberBetween(7, 30)),
        ]);
    }
}
