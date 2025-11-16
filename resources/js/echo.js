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

    // Processing notifications
    channel.listen('.processing.pending', (notification) => {
        console.log('🔥 Processing notification:', notification);
        Livewire.dispatch('slug-processing-updated', {notification});
    });

    // Moderation notifications
    channel.listen('.moderation.approved', (notification) => {
        console.log('🔥 Moderation approved:', notification);
        Livewire.dispatch('slug-moderation-updated', {notification});
    });

    channel.listen('.moderation.rejected', (notification) => {
        console.log('🔥 Moderation rejected:', notification);
        Livewire.dispatch('slug-moderation-updated', {notification});
    });

    channel.listen('.moderation.invalid', (notification) => {
        console.log('🔥 Moderation invalid:', notification);
        Livewire.dispatch('slug-moderation-updated', {notification});
    });

    // Translation notifications
    channel.listen('.translate.successfully', (notification) => {
        console.log('🔥 Translation successful:', notification);
        Livewire.dispatch('slug-translate-updated', {notification});
    });

    channel.listen('.translate.invalid', (notification) => {
        console.log('🔥 Translation invalid:', notification);
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
        // Reload lessons list
        Livewire.dispatch('lesson-regenerated');
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
    });

    channel.listen('.lesson.creation.error', (notification) => {
        console.log('🔥 Lesson creation error:', notification);
        Livewire.dispatch('lesson-notification', {
            id: notification.id,
            entity: notification.entity,
            status: notification.status,
            message: notification.message
        });
    });

    // User auth notifications (welcome & registration)
    // Note: entity will be 'user' (from User class basename)
    channel.listen('.auth.welcome', (notification) => {
        console.log('🔥 User welcome:', notification);
        Livewire.dispatch('user-notification', {
            id: notification.id,
            entity: notification.entity,
            status: notification.status,
            message: notification.message,
            user_id: notification.user_id,
            user_name: notification.user_name
        });
    });

    channel.listen('.auth.registered', (notification) => {
        console.log('🔥 User registered:', notification);
        Livewire.dispatch('user-notification', {
            id: notification.id,
            entity: notification.entity,
            status: notification.status,
            message: notification.message,
            user_id: notification.user_id,
            user_name: notification.user_name
        });
    });

    // Chat channel
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
}