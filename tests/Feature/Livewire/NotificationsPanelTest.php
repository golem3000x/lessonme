<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\Notifications\LessonCreationStatusEnum;
use App\Livewire\Notification\NotificationsPanel;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationsPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.notification.notifications-panel');
    }

    public function test_component_initializes_with_empty_notifications(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class)
            ->assertSet('notifications', []);
    }

    public function test_add_notification_with_array_format(): void
    {
        $notification_data = [
            'id' => 'test-123',
            'entity' => 'lesson',
            'status' => 'successfully',
            'message' => 'Test message',
            'lesson_id' => 1,
            'lesson_url' => '/lessons/1',
            'lesson_name' => 'Test Lesson',
            'language_name' => 'English',
            'user_id' => null,
            'user_name' => null,
        ];

        Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class)
            ->dispatch('lesson-notification', notification: $notification_data)
            ->assertSet('notifications', function ($notifications) {
                return count($notifications) === 1;
            });
    }

    public function test_add_notification_with_named_parameters(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class)
            ->dispatch('lesson-notification',
                id: 'test-456',
                entity: 'lesson',
                status: 'successfully',
                message: 'Test message with params'
            )
            ->assertSet('notifications', function ($notifications) {
                return count($notifications) === 1
                    && $notifications[0]['id'] === 'test-456'
                    && $notifications[0]['message'] === 'Test message with params';
            });
    }

    public function test_add_notification_handles_slug_creation_event(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class)
            ->dispatch('slug-creation-updated',
                id: 'slug-123',
                entity: 'keyword',
                status: 'approved',
                message: 'Keyword created'
            )
            ->assertSet('notifications', function ($notifications) {
                return count($notifications) === 1
                    && $notifications[0]['entity'] === 'keyword';
            });
    }

    public function test_add_notification_handles_slug_moderation_event(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class)
            ->dispatch('slug-moderation-updated',
                id: 'mod-123',
                entity: 'topic',
                status: 'approved',
                message: 'Topic moderated'
            )
            ->assertSet('notifications', function ($notifications) {
                return count($notifications) === 1;
            });
    }

    public function test_add_notification_handles_slug_translate_event(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class)
            ->dispatch('slug-translate-updated',
                id: 'trans-123',
                entity: 'keyword',
                status: 'successfully',
                message: 'Translation completed'
            )
            ->assertSet('notifications', function ($notifications) {
                return count($notifications) === 1;
            });
    }

    public function test_add_notification_handles_user_notification_event(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class)
            ->dispatch('user-notification',
                id: 'user-123',
                entity: 'user',
                status: 'info',
                message: 'User notification'
            )
            ->assertSet('notifications', function ($notifications) {
                return count($notifications) === 1;
            });
    }

    public function test_add_notification_prevents_duplicates(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class);

        // Add same notification twice
        $component->dispatch('lesson-notification',
            id: 'dup-123',
            entity: 'lesson',
            status: 'successfully',
            message: 'Duplicate test'
        );

        $component->dispatch('lesson-notification',
            id: 'dup-456', // Different ID but same content
            entity: 'lesson',
            status: 'successfully',
            message: 'Duplicate test'
        );

        // Should only have one notification
        $component->assertSet('notifications', function ($notifications) {
            return count($notifications) === 1;
        });
    }

    public function test_add_notification_allows_processing_status_duplicates(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class);

        // Add multiple processing notifications for same lesson
        $component->dispatch('lesson-notification',
            id: 'proc-1',
            entity: 'lesson',
            status: LessonCreationStatusEnum::CREATION->value,
            message: 'Processing lesson'
        );

        $component->dispatch('lesson-notification',
            id: 'proc-2',
            entity: 'lesson',
            status: LessonCreationStatusEnum::CREATION->value,
            message: 'Processing lesson'
        );

        // Should allow multiple processing notifications
        $component->assertSet('notifications', function ($notifications) {
            return count($notifications) === 2;
        });
    }

    public function test_add_notification_with_lesson_details(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class)
            ->dispatch('lesson-notification',
                id: 'lesson-123',
                entity: 'lesson',
                status: 'successfully',
                message: 'Lesson created',
                lesson_id: 42,
                lesson_url: '/lessons/42',
                lesson_name: 'Advanced Grammar',
                language_name: 'Vietnamese'
            )
            ->assertSet('notifications', function ($notifications) {
                return $notifications[0]['lesson_id'] === 42
                    && $notifications[0]['lesson_name'] === 'Advanced Grammar'
                    && $notifications[0]['language_name'] === 'Vietnamese';
            });
    }

    public function test_add_notification_with_user_details(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class)
            ->dispatch('user-notification',
                id: 'user-456',
                entity: 'user',
                status: 'info',
                message: 'User action',
                user_id: 10,
                user_name: 'John Doe'
            )
            ->assertSet('notifications', function ($notifications) {
                return $notifications[0]['user_id'] === 10
                    && $notifications[0]['user_name'] === 'John Doe';
            });
    }

    public function test_add_notification_generates_unique_id_if_not_provided(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class)
            ->dispatch('lesson-notification',
                entity: 'lesson',
                status: 'successfully',
                message: 'No ID provided'
            )
            ->assertSet('notifications', function ($notifications) {
                return count($notifications) === 1
                    && isset($notifications[0]['id'])
                    && ! empty($notifications[0]['id']);
            });
    }

    public function test_add_notification_sets_defaults_for_missing_fields(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class)
            ->dispatch('lesson-notification',
                id: 'minimal-123'
            )
            ->assertSet('notifications', function ($notifications) {
                return $notifications[0]['entity'] === 'Entity'
                    && $notifications[0]['status'] === ''
                    && $notifications[0]['message'] === '...';
            });
    }

    public function test_remove_notification_removes_by_id(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class);

        // Add notifications
        $component->dispatch('lesson-notification',
            id: 'remove-1',
            entity: 'lesson',
            status: 'successfully',
            message: 'First'
        );

        $component->dispatch('lesson-notification',
            id: 'remove-2',
            entity: 'lesson',
            status: 'successfully',
            message: 'Second'
        );

        $component->assertSet('notifications', function ($notifications) {
            return count($notifications) === 2;
        });

        // Remove first notification
        $component->dispatch('removeNotification', id: 'remove-1')
            ->assertSet('notifications', function ($notifications) {
                // After filter, array may have non-zero keys
                $notifications_array = array_values($notifications);

                return count($notifications_array) === 1
                    && $notifications_array[0]['id'] === 'remove-2';
            });
    }

    public function test_remove_notification_handles_non_existent_id(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class);

        $component->dispatch('lesson-notification',
            id: 'exists-1',
            entity: 'lesson',
            status: 'successfully',
            message: 'Test'
        );

        // Try to remove non-existent notification
        $component->dispatch('removeNotification', id: 'non-existent')
            ->assertSet('notifications', function ($notifications) {
                return count($notifications) === 1; // Should still have the original
            });
    }

    public function test_remove_notification_removes_all_matching_ids(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class);

        // Add multiple notifications
        $component->dispatch('lesson-notification',
            id: 'same-id',
            entity: 'lesson',
            status: 'successfully',
            message: 'First'
        );

        $component->dispatch('slug-creation-updated',
            id: 'same-id',
            entity: 'keyword',
            status: 'approved',
            message: 'Second'
        );

        // Remove by id - should remove all with that id
        $component->dispatch('removeNotification', id: 'same-id')
            ->assertSet('notifications', []);
    }

    public function test_add_notification_preserves_array_indices(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class);

        // Add 3 notifications
        for ($i = 1; $i <= 3; $i++) {
            $component->dispatch('lesson-notification',
                id: "notif-{$i}",
                entity: 'lesson',
                status: 'successfully',
                message: "Message {$i}"
            );
        }

        $component->assertSet('notifications', function ($notifications) {
            return count($notifications) === 3
                && $notifications[0]['id'] === 'notif-1'
                && $notifications[1]['id'] === 'notif-2'
                && $notifications[2]['id'] === 'notif-3';
        });
    }

    public function test_component_handles_nested_notification_array(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class)
            ->dispatch('lesson-notification', notification: [
                'notification' => [
                    'id' => 'nested-123',
                    'entity' => 'lesson',
                    'status' => 'error',
                    'message' => 'Nested format',
                ],
            ])
            ->assertSet('notifications', function ($notifications) {
                return count($notifications) === 1
                    && $notifications[0]['id'] === 'nested-123'
                    && $notifications[0]['message'] === 'Nested format';
            });
    }

    public function test_multiple_event_types_can_coexist(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(NotificationsPanel::class);

        $component->dispatch('lesson-notification',
            id: 'lesson-1',
            entity: 'lesson',
            status: 'successfully',
            message: 'Lesson notification'
        );

        $component->dispatch('slug-creation-updated',
            id: 'slug-1',
            entity: 'keyword',
            status: 'approved',
            message: 'Slug notification'
        );

        $component->dispatch('user-notification',
            id: 'user-1',
            entity: 'user',
            status: 'info',
            message: 'User notification'
        );

        $component->assertSet('notifications', function ($notifications) {
            return count($notifications) === 3;
        });
    }
}
