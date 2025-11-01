<?php

declare(strict_types=1);

namespace App\Notifications\Keyword;

use App\Models\Keyword\Keyword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KeywordErrorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Keyword $keyword) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.keyword_error_subject'))
            ->line(__('notifications.keyword_error_body', ['keyword' => $this->keyword->translation()?->value ?? $this->keyword->slug]))
            ->line(__('notifications.keyword_error_reason'))
            ->line(__('notifications.thanks'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'keyword_id' => $this->keyword->id,
            'title' => $this->keyword->translation()?->value ?? $this->keyword->slug,
            'message' => __('notifications.keyword_error_db'),
        ];
    }
}
