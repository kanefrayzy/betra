<?php

namespace App\Console\Commands;

use App\Services\WebSocket\Handlers\ConnectionHandler;
use App\Services\WebSocket\Managers\ChannelManager;
use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class WebSocketServer extends Command
{
    protected $signature = 'websocket:serve';
    protected $description = 'Start the WebSocket server';

    public function handle(): void
    {
        $host = config('websocket.host', 'localhost');
        $port = config('websocket.port', 8080);

        $channelManager = new ChannelManager();
        $handler = new ConnectionHandler($channelManager);

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    $handler
                )
            ),
            $port,
            $host
        );

        $this->info("WebSocket server started on ws://{$host}:{$port}");

        $server->run();
    }
}
