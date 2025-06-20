import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Real-time event listeners
window.Echo.private(`chat.${Auth.user.id}`)
    .listen('.new-message', (e) => {
        console.log('New message received:', e);
    });

window.Echo.private(`inventory.${Auth.user.id}`)
    .listen('.stock-update', (e) => {
        console.log('Stock updated:', e);
    });

window.Echo.private(`workforce.${Auth.user.id}`)
    .listen('.shift-change', (e) => {
        console.log('Shift change:', e);
    });

// Product update listener
window.Echo.private(`product.${Auth.user.id}`)
    .listen('.product-update', (e) => {
        console.log('Product updated:', e);
    });
