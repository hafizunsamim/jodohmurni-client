// resources/js/echo.js

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Wajib set global Pusher
window.Pusher = Pusher;

// Wajib set global Echo
window.Echo = new Echo({
    broadcaster: 'reverb',

    // Mesti sama dengan .env → VITE_REVERB_APP_KEY
    key: import.meta.env.VITE_REVERB_APP_KEY,

    wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,

    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    encrypted: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',

    enabledTransports: ['ws', 'wss'],
});
