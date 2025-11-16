<?php

declare(strict_types=1);

namespace App\Notifications\Abstracts;

use BackedEnum;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

abstract class AbstractEntityNotification extends Notification implements ShouldBroadcast
{
    use InteractsWithSockets, Queueable;

    protected string $entity_name;

    public function __construct(protected ?Model $entity)
    {
        if ($entity) {
            $this->entity_name = Str::lcfirst(class_basename($entity));
        } else {
            $this->entity_name = $this->getDefaultEntityName();
        }
    }

    abstract protected function getStatusEnum(): BackedEnum;

    abstract protected function getEventType(): string;

    abstract protected function getDefaultEntityName(): string;

    /**
     * Generate ID for notification
     */
    abstract protected function generateId(): string|int;

    /**
     * Get translation parameters for message
     * @return array<string, mixed>
     */
    abstract protected function getTranslationParams(): array;

    /**
     * Get custom payload data specific to entity type
     * @return array<string, mixed>
     */
    abstract protected function getCustomPayloadData(): array;

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
            'id' => $this->generateId(),
            'status' => $status,
            'entity' => $this->entity_name,
            'message' => __("notifications.{$this->entity_name}_{$event_type}_$status", $this->getTranslationParams()),
        ];

        // Merge custom payload data
        return array_merge($data, $this->getCustomPayloadData());
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
     * Get the channels the event should broadcast on.
     *
     * @param mixed $notifiable
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(mixed $notifiable = null): array
    {
        // Use notifiable's id instead of entity's user_id
        // This works even when entity is null
        $user_id = $notifiable?->id;

        return [new PrivateChannel('App.Models.User.'.$user_id)];
    }

    public function broadcastAs(): string
    {
        return sprintf('%s.%s.%s', $this->entity_name, $this->getEventType(), $this->getStatusEnum()->value);
    }
}
