<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications;

use App\Models\User\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Tests\TestCase;

class ResetPasswordNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $this->token = 'test-reset-token-12345';
    }

    public function test_notification_uses_mail_channel(): void
    {
        $notification = new ResetPasswordNotification($this->token);

        $this->assertEquals(['mail'], $notification->via($this->user));
    }

    public function test_to_mail_returns_mail_message(): void
    {
        $notification = new ResetPasswordNotification($this->token);

        $mail = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mail);
    }

    public function test_mail_contains_reset_url_with_token(): void
    {
        $notification = new ResetPasswordNotification($this->token);

        $mail = $notification->toMail($this->user);

        $action_url = $mail->actionUrl;
        $this->assertStringContainsString($this->token, $action_url);
        $this->assertStringContainsString(urlencode('test@example.com'), $action_url);
    }

    public function test_mail_has_subject(): void
    {
        $notification = new ResetPasswordNotification($this->token);

        $mail = $notification->toMail($this->user);

        $this->assertNotEmpty($mail->subject);
    }

    public function test_mail_has_greeting_with_user_name(): void
    {
        $notification = new ResetPasswordNotification($this->token);

        $mail = $notification->toMail($this->user);

        $this->assertNotNull($mail->greeting);
    }

    public function test_mail_has_action_button(): void
    {
        $notification = new ResetPasswordNotification($this->token);

        $mail = $notification->toMail($this->user);

        $this->assertNotEmpty($mail->actionText);
        $this->assertNotEmpty($mail->actionUrl);
    }

    public function test_notification_is_queueable(): void
    {
        $notification = new ResetPasswordNotification($this->token);

        $this->assertContains(
            \Illuminate\Bus\Queueable::class,
            array_keys(class_uses_recursive($notification))
        );
    }

    public function test_token_is_stored(): void
    {
        $notification = new ResetPasswordNotification($this->token);

        $this->assertEquals($this->token, $notification->token);
    }
}
