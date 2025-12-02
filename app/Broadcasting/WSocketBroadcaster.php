<?php

namespace App\Broadcasting;

use Illuminate\Contracts\Broadcasting\Broadcaster;
use React\Socket\Connector;
use React\EventLoop\Loop;

class WSocketBroadcaster implements Broadcaster
{
    protected string $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function broadcast(array $channels, $event, array $payload = []): void
    {
        $loop = Loop::get();
        $connector = new Connector(loop: $loop);

        $connector->connect($this->url)->then(function ($conn) use ($channels, $event, $payload) {
            $data = [
                'channels' => $channels,
                'event' => $event,
                'payload' => $payload,
            ];

            $conn->send(json_encode($data));
            $conn->close();
        }, function ($e) {
            echo "Не удалось подключиться: {$e->getMessage()}\n";
        });
    }

    public function auth($request)
    {
        //
    }

    public function validAuthenticationResponse($request, $result)
    {
        //
    }
}
