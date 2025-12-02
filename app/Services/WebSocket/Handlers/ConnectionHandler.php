<?php

namespace App\Services\WebSocket\Handlers;

use App\Services\WebSocket\Managers\ChannelManager;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class ConnectionHandler implements MessageComponentInterface
{
    protected array $connections = [];
    protected ChannelManager $channelManager;
    protected int $totalConnections = 0;

    public function __construct(ChannelManager $channelManager)
    {
        $this->channelManager = $channelManager;
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $this->connections[$conn->resourceId] = $conn;
        $this->totalConnections++;

        $this->broadcastMessage("New connection established (ID: {$conn->resourceId})");

        $this->broadcastTotalConnections();

        echo "New connection (ID: {$conn->resourceId})" . PHP_EOL;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
    }

    public function onClose(ConnectionInterface $conn): void
    {
        unset($this->connections[$conn->resourceId]);
        $this->totalConnections--;

        $this->broadcastTotalConnections();

        echo "Connection closed (ID: {$conn->resourceId})" . PHP_EOL;
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        echo "Error on connection (ID: {$conn->resourceId}): {$e->getMessage()}" . PHP_EOL;
        $conn->close();
    }

    protected function broadcastMessage($message): void
    {
        foreach ($this->connections as $conn) {
            $conn->send($message);
        }
    }

    protected function broadcastTotalConnections(): void
    {
        foreach ($this->connections as $conn) {
            $conn->send("Total connections: {$this->totalConnections}");
        }
    }
}
