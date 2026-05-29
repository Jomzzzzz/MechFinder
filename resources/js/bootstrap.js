import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;

if (pusherKey) {
    import('laravel-echo').then(({ default: Echo }) => {
        import('pusher-js').then(({ default: Pusher }) => {
            window.Pusher = Pusher;
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: pusherKey,
                cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'ap1',
                forceTLS: true,
            });
        });
    });
}
