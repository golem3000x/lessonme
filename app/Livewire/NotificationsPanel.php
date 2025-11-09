<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationsPanel extends Component
{
    /** @var array<int, array{id: string, entity: string, status: string, message: string, lesson_id: int|null, lesson_url: string|null, lesson_name: string|null}> */
    public array $notifications = [];

    #[On('slug-processing-updated')]
    #[On('slug-moderation-updated')]
    #[On('slug-translate-updated')]
    #[On('lesson-notification')]
    public function addNotification(mixed $notification = null, mixed $id = null, mixed $entity = null, mixed $status = null, mixed $message = null, mixed $lesson_id = null, mixed $lesson_url = null, mixed $lesson_name = null): void
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

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.notifications-panel');
    }
}
