<?php

declare(strict_types=1);

namespace Database\Factories\Support;

use App\Models\Support\ChatSession;
use App\Models\Support\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chat_session_id' => ChatSession::factory(),
            'user_id' => User::factory(),
            'message' => fake()->sentence(),
            'is_from_admin' => fake()->boolean(30), // 30% chance of being from admin
            'is_read' => fake()->boolean(70), // 70% chance of being read
        ];
    }

    /**
     * Indicate that the message is from admin.
     */
    public function fromAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_from_admin' => true,
        ]);
    }

    /**
     * Indicate that the message is from user.
     */
    public function fromUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_from_admin' => false,
        ]);
    }

    /**
     * Indicate that the message is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    /**
     * Indicate that the message is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
        ]);
    }

    /**
     * Indicate that the message has specific content.
     */
    public function withMessage(string $message): static
    {
        return $this->state(fn (array $attributes) => [
            'message' => $message,
        ]);
    }

    /**
     * Indicate that the message is a long message.
     */
    public function longMessage(): static
    {
        return $this->state(fn (array $attributes) => [
            'message' => fake()->paragraph(5),
        ]);
    }

    /**
     * Indicate that the message is a short message.
     */
    public function shortMessage(): static
    {
        return $this->state(fn (array $attributes) => [
            'message' => fake()->word(),
        ]);
    }
}
