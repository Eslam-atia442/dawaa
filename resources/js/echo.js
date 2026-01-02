import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '79402e7ec57b73114402',
    cluster: 'mt1',
    authEndpoint:  '/admin/broadcasting/auth',
    forceTLS: true
});
