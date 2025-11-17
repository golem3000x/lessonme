<?php

declare(strict_types=1);

namespace App\Notifications\Abstracts;

use App\Models\Lesson\Lesson;
use BackedEnum;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

abstract class AbstractLessonNotification extends Notification implements ShouldBroadcast
{
    use InteractsWithSockets, Queueable;

    protected string $entity_name;

    public function __construct(
        protected ?Lesson $entity
    )
    {
        if ($entity) {
            $this->entity_name = Str::lcfirst(class_basename($entity));
        } else {
            $this->entity_name = 'lesson';
        }
    }

    abstract protected function getStatusEnum(): BackedEnum;

    abstract protected function getEventType(): string;

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
        $status = $this->getStatusEnum()->value;
        $event_type = $this->getEventType();

        $data = [
            'id' => uniqid("lesson_{$event_type}_"),
            'status' => $status,
            'entity' => $this->entity_name,
            'message' => __("notifications.{$this->entity_name}_{$event_type}_$status", [
                'name' => $this->entity !== null ? $this->entity->lesson_name : '',
            ]),
        ];

        if ($this->entity) {
            $data['lesson_id'] = $this->entity->id;
            $data['lesson_url'] = route('lessons.show', $this->entity->id);
            $data['lesson_name'] = $this->entity->lesson_name;
        }

        return $data;
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
        $user_id = $this->entity?->user_id;

        return [new PrivateChannel('App.Models.User.'.$user_id)];
    }

    public function broadcastAs(): string
    {
        return sprintf('%s.%s.%s', $this->entity_name, $this->getEventType(), $this->getStatusEnum()->value);
    }
}
