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
}