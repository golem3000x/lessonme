<?php

declare(strict_types=1);

namespace App\Notifications\Topic;

use App\Models\Topic\Topic;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TopicApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Topic $topic) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.topic_approved_subject'))
            ->line(__('notifications.topic_approved_body', ['topic' => $this->topic->translation()?->value ?? $this->topic->slug]))
            ->action(__('notifications.view_topics'), url('/topics'))
            ->line(__('notifications.thanks'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'topic_id' => $this->topic->id,
            'title' => $this->topic->translation()?->value ?? $this->topic->slug,
            'message' => __('notifications.topic_approved_db'),
        ];
    }
}
