<?php

declare(strict_types=1);

namespace App\Notifications\Auth;

use App\Enums\AuthNotificationStatusEnum;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class WelcomeNotification extends Notification implements ShouldBroadcast
{
    use InteractsWithSockets, Queueable;

    protected string $entity_name;

    public function __construct(
        protected User $user,
        protected string $message
    ) {
        $this->entity_name = Str::lcfirst(class_basename($user));
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
        return [
            'id' => uniqid('auth_welcome_'),
            'status' => AuthNotificationStatusEnum::WELCOME->value,
            'entity' => $this->entity_name,
            'message' => $this->message,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
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
        return [new PrivateChannel('App.Models.User.'.$this->user->id)];
    }

    public function broadcastAs(): string
    {
        return 'auth.'.AuthNotificationStatusEnum::WELCOME->value;
    }
}
