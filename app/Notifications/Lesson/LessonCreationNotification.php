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

class LessonCreationNotification extends Notification implements ShouldBroadcast
{
    use InteractsWithSockets, Queueable;

    public function __construct(
        protected ?Lesson $lesson,
        protected string $status,
        protected string $message
    ) {}

    public function via(): array
    {
        return ['database', 'broadcast'];
    }

    protected function payload(): array
    {
        $data = [
            'id' => uniqid('lesson_creation_'),
            'status' => $this->status,
            'entity' => 'lesson',
            'message' => $this->message,
        ];

        if ($this->lesson) {
            $data['lesson_id'] = $this->lesson->id;
            $data['lesson_url'] = route('lessons.show', $this->lesson->id);
            $data['lesson_name'] = $this->lesson->lesson_name;
        }

        return $data;
    }

    public function toArray(): array
    {
        return $this->payload();
    }

    public function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage($this->payload());
    }

    public function broadcastOn(): PrivateChannel
    {
        $user_id = $this->lesson?->user_id ?? auth()->id();

        return new PrivateChannel('App.Models.User.'.$user_id);
    }

    public function broadcastAs(): string
    {
        return 'lesson.creation.'.$this->status;
    }
}
