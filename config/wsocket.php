<?php

return [
    'host' => env('WEBSOCKET_HOST', '127.0.0.1'),
    'port' => env('WEBSOCKET_PORT', 8080),
    'broadcast_driver' => env('BROADCAST_DRIVER', 'pusher'),
];
