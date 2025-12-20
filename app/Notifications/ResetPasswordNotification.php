<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $token
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(CanResetPassword $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Get name safely - CanResetPassword interface doesn't define $name property
        $name = $notifiable instanceof \App\Models\User\User ? $notifiable->name : 'User';

        return (new MailMessage)
            ->subject(Lang::get('passwords.reset_email_subject'))
            ->greeting(Lang::get('passwords.greeting', ['name' => $name]))
            ->line(Lang::get('passwords.reset_email_line1'))
            ->action(Lang::get('passwords.reset_email_action'), $url)
            ->line(Lang::get('passwords.reset_email_line2', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('passwords.reset_email_line3'));
    }
}
