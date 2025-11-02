<?php

declare(strict_types=1);

namespace App\Notifications\Abstracts;

use App\Models\Abstracts\Slug\SlugEntity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

abstract class SlugEntityErrorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected SlugEntity $entity) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $entityName = $this->getEntityName();
        $entityTitle = $this->entity->translatedValue();

        return (new MailMessage)
            ->subject(__("notifications.{$entityName}_error_subject"))
            ->line(__("notifications.{$entityName}_error_body", [$entityName => $entityTitle]))
            ->line(__("notifications.{$entityName}_error_reason"))
            ->line(__('notifications.thanks'));
    }

    public function toArray(object $notifiable): array
    {
        $entityName = $this->getEntityName();

        return [
            $this->getEntityIdKey() => $this->entity->id,
            'title' => $this->entity->translatedValue(),
            'message' => __("notifications.{$entityName}_error_db"),
        ];
    }

    /**
     * Get entity name for translation keys (e.g., 'topic', 'keyword')
     */
    abstract protected function getEntityName(): string;

    /**
     * Get entity ID key for array (e.g., 'topic_id', 'keyword_id')
     */
    abstract protected function getEntityIdKey(): string;
}
