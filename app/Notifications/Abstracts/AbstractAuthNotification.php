<?php

declare(strict_types=1);

namespace App\Notifications\Abstracts;

use App\Models\User;
use BackedEnum;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

abstract class AbstractAuthNotification extends Notification implements ShouldBroadcast
{
    use InteractsWithSockets, Queueable;

    protected string $entity_name;

    public function __construct(protected ?User $entity, protected string $message = '')
    {
        $this->entity_name = $entity ? Str::lcfirst(class_basename($entity)) : 'user';
    }

    abstract protected function getStatusEnum(): BackedEnum;

    protected function getEventType(): string
    {
        return 'auth';
    }

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
        
        $data = [
            'id' => uniqid("auth_{$status}_"),
            'status' => $status,
            'entity' => $this->entity_name,
        ];
        
        if ($this->entity) {
            $data['user_id'] = $this->entity->id;
            $data['user_name'] = $this->entity->name;
        }
        
        // Always override message with custom greeting-based message
        // Auth notifications always use time-based greetings from GreetingService
        if (!empty($this->message)) {
            $data['message'] = $this->message;
        } else {
            $data['message'] = __("notifications.{$this->entity_name}_{$this->getEventType()}_$status", [
                'name' => $this->entity?->name ?? '',
            ]);
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
        return [new PrivateChannel('App.Models.User.'.($this->entity?->id ?? 0))];
    }

    public function broadcastAs(): string
    {
        return sprintf('%s.%s.%s', $this->entity_name, $this->getEventType(), $this->getStatusEnum()->value);
    }
}
