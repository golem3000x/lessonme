<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\EntityTypeEnum;
use App\Enums\LessonCreationStatusEnum;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationsPanel extends Component
{
    /** @var array<int, array{id: string, entity: string, status: string, message: string, lesson_id: int|null, lesson_url: string|null, lesson_name: string|null, user_id: int|null, user_name: string|null}> */
    public array $notifications = [];

    #[On('slug-processing-updated')]
    #[On('slug-moderation-updated')]
    #[On('slug-translate-updated')]
    #[On('lesson-notification')]
    #[On('user-notification')]
    public function addNotification(mixed $notification = null, mixed $id = null, mixed $entity = null, mixed $status = null, mixed $message = null, mixed $lesson_id = null, mixed $lesson_url = null, mixed $lesson_name = null, mixed $user_id = null, mixed $user_name = null): void
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
            $user_id = $data['user_id'] ?? null;
            $user_name = $data['user_name'] ?? null;
        }

        $notification_data = [
            'id' => $id ?? uniqid(),
            'entity' => $entity ?? 'Entity',
            'status' => $status ?? '',
            'message' => $message ?? '...',
            'lesson_id' => $lesson_id,
            'lesson_url' => $lesson_url,
            'lesson_name' => $lesson_name,
            'user_id' => $user_id,
            'user_name' => $user_name,
        ];

        // Check for duplicates to avoid spam
        // For lessons, check by message and lesson_id to allow regeneration of the same lesson
        $exists = collect($this->notifications)->contains(function ($n) use ($notification_data) {
            $basic_match = $n['entity'] === $notification_data['entity'] &&
                          $n['status'] === $notification_data['status'] &&
                          $n['message'] === $notification_data['message'];

            // For lesson entity with processing status, don't check as duplicate
            // to allow multiple regenerations
            if ($notification_data['entity'] === EntityTypeEnum::LESSON->value && $notification_data['status'] === LessonCreationStatusEnum::PROCESSING->value) {
                return false;
            }

            return $basic_match;
        });

        if (! $exists) {
            $this->notifications[] = $notification_data;
        }
    }

    #[On('removeNotification')]
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
