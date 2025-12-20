<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User\UserMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly UserMessage $message
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
            'sender_id' => $this->message->sender_id,
            'recipient_id' => $this->message->recipient_id,
            'content' => $this->message->content,
            'read_at' => $this->message->read_at?->toIso8601String(),
            'created_at' => $this->message->created_at?->toIso8601String(),
            'sender_name' => $this->message->sender->full_name,
            'sender_avatar' => $this->message->sender->avatar_url,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // Send to recipient's private channel
            new PrivateChannel('dm.'.$this->message->recipient_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'dm.sent';
    }
}
