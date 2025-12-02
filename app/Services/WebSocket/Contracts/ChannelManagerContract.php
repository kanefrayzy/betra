<?php

namespace App\Services\WebSocket\Contracts;

use Ratchet\ConnectionInterface;

interface ChannelManagerContract
{
    public function subscribe(ConnectionInterface $conn, string $channel): void;

    public function unsubscribe(ConnectionInterface $conn, string $channel): void;

    public function broadcast(string $channel, string $message, ConnectionInterface $from): void;

    public function removeConnection(ConnectionInterface $conn): void;
}
