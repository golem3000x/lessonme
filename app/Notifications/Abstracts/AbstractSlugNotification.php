<?php

declare(strict_types=1);

namespace App\Notifications\Abstracts;

use App\Models\Abstracts\Slug\Slug;
use BackedEnum;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

abstract class AbstractSlugNotification extends Notification implements ShouldBroadcast
{
    use InteractsWithSockets, Queueable;

    protected string $entity_name;

    /**
     * @param Slug<*, *> $entity
     */
    public function __construct(protected Slug $entity)
    {
        $this->entity_name = Str::lcfirst(class_basename($entity));
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

        return [
            'id' => $this->entity->id,
            'status' => $status,
            'entity' => $this->entity_name,
            'slug' => $this->entity->slug,
            'message' => __("notifications.{$this->entity_name}_{$this->getEventType()->value}_$status", [
                'slug' => $this->entity->slug,
            ]),
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
        return [new PrivateChannel('App.Models.User.'.$this->entity->user_id)];
    }

    public function broadcastAs(): string
    {
        return sprintf('%s.%s.%s', $this->entity_name, $this->getEventType()->value, $this->getStatusEnum()->value);
    }
}
