<?php

declare(strict_types=1);

namespace App\Notifications\Abstracts;

use App\Models\Lesson\Lesson;
use App\Models\User;
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

    protected ?Lesson $entity = null;

    public function __construct(
        protected User $user,
        protected ?int $entity_id
    ) {
        if ($this->entity_id) {
            $this->entity = Lesson::find($this->entity_id);
        }
        $this->entity_name = Str::lcfirst(class_basename(new Lesson));
    }

    abstract protected function getStatusEnum(): BackedEnum;

    abstract protected function getEventType(): BackedEnum;

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
            'id' => uniqid("lesson_{$event_type->value}_"),
            'status' => $status,
            'entity' => $this->entity_name,
            'message' => __("notifications.{$this->entity_name}_{$event_type->value}_$status", [
                'name' => $this->entity !== null ? $this->entity->lesson_name : '',
            ]),
        ];

        // Include lesson details only for successfully created lessons
        if ($this->entity) {
            $data['lesson_id'] = $this->entity->uuid;
            $data['lesson_url'] = route('lessons.show', $this->entity->uuid);
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
        return [new PrivateChannel("App.Models.User.{$this->user->id}")];
    }

    public function broadcastAs(): string
    {
        return sprintf('%s.%s.%s', $this->entity_name, $this->getEventType()->value, $this->getStatusEnum()->value);
    }
}
