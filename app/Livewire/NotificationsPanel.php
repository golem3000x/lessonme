<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class NotificationsPanel extends Component
{
    public array $notifications = [];

    #[On('slug-processing-updated')]
    #[On('slug-moderation-updated')]
    #[On('slug-translate-updated')]
    #[On('lesson-notification')]
    public function addNotification($notification = null, $id = null, $entity = null, $status = null, $message = null, $lesson_id = null, $lesson_url = null, $lesson_name = null): void
    {
        // Handle both old format (array) and new format (named parameters)
        if ($notification && is_array($notification)) {
            $data = $notification['notification'] ?? $notification;
            $id = $data['id'] ?? uniqid();
            $entity = $data['entity'] ?? 'Entity';
            $status = $data['status'] ?? '';
            $message = $data['message'] ?? '...';
            $lesson_id = $data['lesson_id'] ?? null;
            $lesson_url = $data['lesson_url'] ?? null;
            $lesson_name = $data['lesson_name'] ?? null;
        }

        $notification_data = [
            'id' => $id ?? uniqid(),
            'entity' => $entity ?? 'Entity',
            'status' => $status ?? '',
            'message' => $message ?? '...',
            'lesson_id' => $lesson_id,
            'lesson_url' => $lesson_url,
            'lesson_name' => $lesson_name,
        ];

        // Check for duplicates to avoid spam
        $exists = collect($this->notifications)->contains(function ($n) use ($notification_data) {
            return $n['entity'] === $notification_data['entity'] &&
                   $n['status'] === $notification_data['status'] &&
                   $n['message'] === $notification_data['message'];
        });

        if (! $exists) {
            $this->notifications[] = $notification_data;
            $this->dispatch('remove-notification', ['id' => $notification_data['id']]);
        }
    }

    public function removeNotification(string $id): void
    {
        $this->notifications = array_filter(
            $this->notifications,
            fn ($n) => $n['id'] !== $id
        );
    }

    public function render()
    {
        return view('livewire.notifications-panel');
    }
}
