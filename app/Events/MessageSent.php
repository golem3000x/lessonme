<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Support\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Message $message
    ) {}

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'chat_session_id' => $this->message->chat_session_id,
            'user_id' => $this->message->user_id,
            'message' => $this->message->message,
            'is_from_admin' => $this->message->is_from_admin,
            'is_read' => $this->message->is_read,
            'created_at' => $this->message->created_at?->toIso8601String(),
            'user_name' => $this->message->user->full_name,
            'user_avatar' => $this->message->user->avatar,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            // Send to user's channel
            new PrivateChannel('chat.'.$this->message->user_id),
        ];

        // If message from user, also send to admins
        if (! $this->message->is_from_admin) {
            $channels[] = new PrivateChannel('admin-chat');
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
