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

    // Processing notifications for Topics
    channel.listen('.topic.processing.pending', (notification) => {
        console.log('🔥 Topic processing:', notification);
        Livewire.dispatch('slug-processing-updated', {notification});
    });

    // Processing notifications for Keywords
    channel.listen('.keyword.processing.pending', (notification) => {
        console.log('🔥 Keyword processing:', notification);
        Livewire.dispatch('slug-processing-updated', {notification});
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
    // Format: user.auth.status
    channel.listen('.user.auth.welcome', (notification) => {
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

    channel.listen('.user.auth.registered', (notification) => {
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