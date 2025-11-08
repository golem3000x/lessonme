<?php

declare(strict_types=1);

namespace App\Notifications\Abstracts;

use App\Models\Abstracts\Slug\SlugEntity;
use BackedEnum;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

abstract class AbstractSlugEntityNotification extends Notification implements ShouldBroadcast
{
    use InteractsWithSockets, Queueable;

    protected string $entity_name;

    public function __construct(protected SlugEntity $entity)
    {
        $this->entity_name = Str::lcfirst(class_basename($entity));
    }

    abstract protected function getStatusEnum(): BackedEnum;

    abstract protected function getEventType(): string;

    public function via(): array
    {
        return ['database', 'broadcast'];
    }

    protected function payload(): array
    {
        $status = $this->getStatusEnum()->value;

        return [
            'id' => $this->entity->id,
            'status' => $status,
            'entity' => $this->entity_name,
            'slug' => $this->entity->slug,
            'message' => __("notifications.{$this->entity_name}_{$this->getEventType()}_$status", [
                'slug' => $this->entity->slug,
            ]),
        ];
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
        return new PrivateChannel('App.Models.User.'.$this->entity->user_id);
    }

    public function broadcastAs(): string
    {
        return sprintf('%s.%s', $this->getEventType(), $this->getStatusEnum()->value);
    }
}
