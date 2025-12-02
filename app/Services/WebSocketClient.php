<?php

namespace App\Services;

use Ratchet\Client\Connector;
use React\EventLoop\Loop;

class WebSocketClient
{
    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function send($message)
    {
        $loop = Loop::get();

        $connector = new Connector($loop);

        $connector($this->url)->then(function($conn) use ($message, $loop) {
            $conn->send($message);
            $conn->close();
            $loop->stop();
        }, function ($e) use ($loop) {
            echo "Could not connect: {$e->getMessage()}\n";
            $loop->stop();
        });

        $loop->run();
    }
}
