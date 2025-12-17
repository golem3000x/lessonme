<?php

declare(strict_types=1);

namespace App\Notifications\Abstracts;

use App\Models\Course\Course;
use App\Models\User;
use BackedEnum;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

abstract class AbstractCourseNotification extends Notification implements ShouldBroadcast
{
    use InteractsWithSockets, Queueable;

    protected string $entity_name;

    protected ?Course $entity = null;

    public function __construct(
        protected User $user,
        protected ?int $entity_id
    ) {
        if ($this->entity_id) {
            $this->entity = Course::find($this->entity_id);
        }
        $this->entity_name = Str::lcfirst(class_basename(new Course));
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
            'id' => uniqid("course_{$event_type->value}_"),
            'status' => $status,
            'entity' => $this->entity_name,
            'message' => __("notifications.{$this->entity_name}_{$event_type->value}_$status", [
                'name' => $this->entity !== null ? $this->entity->title : '',
            ]),
        ];

        if ($this->entity) {
            $data['course_id'] = $this->entity->uuid;
            $data['course_database_id'] = $this->entity->id;
            $data['course_url'] = route('courses.show', $this->entity->uuid);
            $data['course_name'] = $this->entity->title;
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
