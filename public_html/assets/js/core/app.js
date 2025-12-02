import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true, // включите эту опцию, если ваш сервер использует HTTPS
    wsHost: window.location.hostname,
    wsPort: 6001, // порт, на котором работает Laravel Echo Server
    disableStats: true, // отключите эту опцию, если вы хотите получать статистику от сервера Echo
});
