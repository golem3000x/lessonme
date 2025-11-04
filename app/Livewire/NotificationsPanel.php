<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class NotificationsPanel extends Component
{
    public array $notifications = [];

    #[On('slug-moderation-updated')]
    #[On('slug-translate-updated')]
    public function addNotification($notification): void
    {
        $data = $notification['notification'] ?? $notification;

        $this->notifications[] = [
            'id' => $data['id'] ?? uniqid(),
            'entity' => $data['entity'] ?? 'Entity',
            'status' => $data['status'] ?? '',
            'message' => $data['message'] ?? '...',
        ];

        $this->dispatch('remove-notification', ['id' => end($this->notifications)['id']]);
    }

    public function removeNotification(string $id): void
    {
        $this->notifications = array_filter(
            $this->notifications,
            fn($n) => $n['id'] !== $id
        );
    }

    public function render()
    {
        return view('livewire.notifications-panel');
    }
}
