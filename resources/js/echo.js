import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

const userIdMeta = document.querySelector('meta[name="user-id"]');
const userId = userIdMeta ? userIdMeta.getAttribute('content') : null;

if (userId) {
    const channel = window.Echo.private(`App.Models.User.${userId}`);

    // Creation notifications for Topics
    channel.listen('.topic.creation.pending', (notification) => {
        console.log('🔥 Topic creation:', notification);
        Livewire.dispatch('slug-creation-updated', {notification});
    });

    // Creation notifications for Keywords
    channel.listen('.keyword.creation.pending', (notification) => {
        console.log('🔥 Keyword creation:', notification);
        Livewire.dispatch('slug-creation-updated', {notification});
    });

    // Moderation notifications for Topics
    channel.listen('.topic.moderation.approved', (notification) => {
        console.log('🔥 Topic moderation approved:', notification);
        Livewire.dispatch('slug-moderation-updated', {notification});
    });

    channel.listen('.topic.moderation.rejected', (notification) => {
        console.log('🔥 Topic moderation rejected:', notification);
        Livewire.dispatch('slug-moderation-updated', {notification});
    });

    channel.listen('.topic.moderation.invalid', (notification) => {
        console.log('🔥 Topic moderation invalid:', notification);
        Livewire.dispatch('slug-moderation-updated', {notification});
    });

    // Moderation notifications for Keywords
    channel.listen('.keyword.moderation.approved', (notification) => {
        console.log('🔥 Keyword moderation approved:', notification);
        Livewire.dispatch('slug-moderation-updated', {notification});
    });

    channel.listen('.keyword.moderation.rejected', (notification) => {
        console.log('🔥 Keyword moderation rejected:', notification);
        Livewire.dispatch('slug-moderation-updated', {notification});
    });

    channel.listen('.keyword.moderation.invalid', (notification) => {
        console.log('🔥 Keyword moderation invalid:', notification);
        Livewire.dispatch('slug-moderation-updated', {notification});
    });

    // Translation notifications for Topics
    channel.listen('.topic.translate.successfully', (notification) => {
        console.log('🔥 Topic translation successful:', notification);
        Livewire.dispatch('slug-translate-updated', {notification});
    });

    channel.listen('.topic.translate.invalid', (notification) => {
        console.log('🔥 Topic translation invalid:', notification);
        Livewire.dispatch('slug-translate-updated', {notification});
    });

    // Translation notifications for Keywords
    channel.listen('.keyword.translate.successfully', (notification) => {
        console.log('🔥 Keyword translation successful:', notification);
        Livewire.dispatch('slug-translate-updated', {notification});
    });

    channel.listen('.keyword.translate.invalid', (notification) => {
        console.log('🔥 Keyword translation invalid:', notification);
        Livewire.dispatch('slug-translate-updated', {notification});
    });

    // Lesson creation notifications
    channel.listen('.lesson.creation.processing', (notification) => {
        console.log('🔥 Lesson creation processing:', notification);
        Livewire.dispatch('lesson-notification', {
            id: notification.id,
            entity: notification.entity,
            status: notification.status,
            message: notification.message
        });
    });

    channel.listen('.lesson.creation.successfully', (notification) => {
        console.log('🔥 Lesson creation success:', notification);
        Livewire.dispatch('lesson-notification', {
            id: notification.id,
            entity: notification.entity,
            status: notification.status,
            message: notification.message,
            lesson_id: notification.lesson_id,
            lesson_url: notification.lesson_url,
            lesson_name: notification.lesson_name
        });
        // Reload lessons list and clear placeholders
        Livewire.dispatch('lesson-created', {
            lesson_database_id: notification.lesson_database_id
        });
    });

    channel.listen('.lesson.creation.exists', (notification) => {
        console.log('🔥 Lesson creation exists:', notification);
        Livewire.dispatch('lesson-notification', {
            id: notification.id,
            entity: notification.entity,
            status: notification.status,
            message: notification.message,
            lesson_id: notification.lesson_id,
            lesson_url: notification.lesson_url,
            lesson_name: notification.lesson_name
        });
        // Clear placeholders when lesson already exists (count won't increase!)
        Livewire.dispatch('lesson-created', {
            lesson_database_id: notification.lesson_database_id,
            force_cleanup: true  // Force cleanup since count won't change
        });
    });

    channel.listen('.lesson.creation.error', (notification) => {
        console.log('🔥 Lesson creation error:', notification);
        Livewire.dispatch('lesson-notification', {
            id: notification.id,
            entity: notification.entity,
            status: notification.status,
            message: notification.message
        });
        // Clear placeholders on error (count won't increase!)
        Livewire.dispatch('lesson-created', {
            lesson_database_id: notification.lesson_database_id,
            force_cleanup: true  // Force cleanup since count won't change
        });
    });

    // Lesson regeneration notifications
    channel.listen('.lesson.regeneration.processing', (notification) => {
        console.log('🔥 Lesson regeneration processing:', notification);
        Livewire.dispatch('lesson-notification', {
            id: notification.id,
            entity: notification.entity,
            status: notification.status,
            message: notification.message
        });
    });

    channel.listen('.lesson.regeneration.successfully', (notification) => {
        console.log('🔥 Lesson regeneration success:', notification);
        Livewire.dispatch('lesson-notification', {
            id: notification.id,
            entity: notification.entity,
            status: notification.status,
            message: notification.message,
            lesson_id: notification.lesson_id,
            lesson_url: notification.lesson_url,
            lesson_name: notification.lesson_name
        });
        // Reload lesson details if on show page
        Livewire.dispatch('lesson-regenerated', {
            lesson_database_id: notification.lesson_database_id
        });
    });

    channel.listen('.lesson.regeneration.exists', (notification) => {
        console.log('🔥 Lesson regeneration exists:', notification);
        Livewire.dispatch('lesson-notification', {
            id: notification.id,
            entity: notification.entity,
            status: notification.status,
            message: notification.message,
            lesson_id: notification.lesson_id,
            lesson_url: notification.lesson_url,
            lesson_name: notification.lesson_name
        });
        // Clear placeholders when lesson already exists
        Livewire.dispatch('lesson-regenerated', {
            lesson_database_id: notification.lesson_database_id,
            force_cleanup: true
        });
    });

    channel.listen('.lesson.regeneration.error', (notification) => {
        console.log('🔥 Lesson regeneration error:', notification);
        Livewire.dispatch('lesson-notification', {
            id: notification.id,
            entity: notification.entity,
            status: notification.status,
            message: notification.message
        });
        // Clear placeholders on error
        Livewire.dispatch('lesson-regenerated', {
            lesson_database_id: notification.lesson_database_id,
            force_cleanup: true
        });
    });

    // Lesson assignment notification (for student - receiving assignment)
    channel.listen('.lesson.assignment.assigned', (notification) => {
        console.log('🔥 Lesson assigned:', notification);
        Livewire.dispatch('lesson-notification', {
            id: notification.id,
            entity: notification.entity,
            status: notification.status,
            message: notification.message,
            lesson_id: notification.lesson_id,
            lesson_url: notification.lesson_url,
            lesson_name: notification.lesson_name
        });
    });

    // Lesson assignment sent notification (for teacher - confirmation)
    channel.listen('.lesson.assignment.sent', (notification) => {
        console.log('🔥 Lesson assignment sent:', notification);
        Livewire.dispatch('lesson-notification', {
            id: notification.id,
            entity: notification.entity,
            status: notification.status,
            message: notification.message,
            lesson_id: notification.lesson_id,
            lesson_url: notification.lesson_url,
            lesson_name: notification.lesson_name
        });
    });

    // Lesson unassigned notification (for student - removed from lesson)
    channel.listen('.lesson.assignment.unassigned', (notification) => {
        console.log('🔥 Lesson unassigned:', notification);
        Livewire.dispatch('lesson-notification', {
            id: notification.id,
            entity: notification.entity,
            status: notification.status,
            message: notification.message,
            lesson_id: notification.lesson_id,
            lesson_url: notification.lesson_url,
            lesson_name: notification.lesson_name
        });
    });

    // Lesson assignment removed notification (for teacher - confirmation)
    channel.listen('.lesson.assignment.removed', (notification) => {
        console.log('🔥 Lesson assignment removed:', notification);
        Livewire.dispatch('lesson-notification', {
            id: notification.id,
            entity: notification.entity,
            status: notification.status,
            message: notification.message,
            lesson_id: notification.lesson_id,
            lesson_url: notification.lesson_url,
            lesson_name: notification.lesson_name
        });
    });

    // Chat channel (Support)
    const chatChannel = window.Echo.private(`chat.${userId}`);
    
    chatChannel.listen('.message.sent', (message) => {
        console.log('🔥 Chat message received:', message);
        Livewire.dispatch('message-received', {
            id: message.id,
            chat_session_id: message.chat_session_id,
            user_id: message.user_id,
            message: message.message,
            is_from_admin: message.is_from_admin,
            is_read: message.is_read,
            created_at: message.created_at,
            user_name: message.user_name,
            user_avatar: message.user_avatar
        });
    });

    // Direct Messages channel
    const dmChannel = window.Echo.private(`dm.${userId}`);
    
    dmChannel.listen('.dm.sent', (message) => {
        console.log('🔥 DM received:', message);
        Livewire.dispatch('dm-received', {
            id: message.id,
            sender_id: message.sender_id,
            recipient_id: message.recipient_id,
            content: message.content,
            read_at: message.read_at,
            created_at: message.created_at,
            sender_name: message.sender_name,
            sender_avatar: message.sender_avatar
        });
    });
}