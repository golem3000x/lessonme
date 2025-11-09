<?php

declare(strict_types=1);

namespace App\Notifications\Lesson;

use App\Models\Lesson;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class LessonNotification extends Notification implements ShouldBroadcast
{
    use InteractsWithSockets, Queueable;

    public function __construct(
        protected Lesson $lesson,
        protected string $status,
        protected string $message
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * @return array<string, mixed>
     */
    protected function payload(): array
    {
        return [
            'id' => uniqid('lesson_'),
            'status' => $this->status,
            'entity' => 'lesson',
            'message' => $this->message,
            'lesson_id' => $this->lesson->id,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->payload();
    }

    public function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage($this->payload());
    }

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('App.Models.User.'.$this->lesson->user_id)];
    }

    public function broadcastAs(): string
    {
        return 'lesson.'.$this->status;
    }
}
