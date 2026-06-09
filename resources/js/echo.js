import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const pusherClient = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'ap1',
    forceTLS: true
});

window.Echo = new Echo({
    broadcaster: 'pusher',
    client: pusherClient,
});
