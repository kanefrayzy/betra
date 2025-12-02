<?php

namespace App\Services\WebSocket\Managers;

use App\Services\WebSocket\Contracts\ChannelManagerContract;
use Ratchet\ConnectionInterface;

class ChannelManager implements ChannelManagerContract
{
    protected array $channels = [];

    public function subscribe(ConnectionInterface $conn, string $channel): void
    {
        if (!isset($this->channels[$channel])) {
            $this->channels[$channel] = [];
        }

        $this->channels[$channel][$conn->resourceId] = $conn;
        $conn->send(json_encode(['success' => "Подписка на канал {$channel} выполнена"]));
    }

    public function unsubscribe(ConnectionInterface $conn, string $channel): void
    {
        if (isset($this->channels[$channel][$conn->resourceId])) {
            unset($this->channels[$channel][$conn->resourceId]);
            $conn->send(json_encode(['success' => "Отписка от канала {$channel} выполнена"]));
            if (empty($this->channels[$channel])) {
                unset($this->channels[$channel]);
            }
        }
    }

    public function broadcast(string $channel, string $message, ConnectionInterface $from): void
    {
        if (isset($this->channels[$channel])) {
            foreach ($this->channels[$channel] as $client) {
                if ($from !== $client) {
                    $client->send(json_encode(['channel' => $channel, 'message' => $message]));
                }
            }
        }
    }

    public function removeConnection(ConnectionInterface $conn): void
    {
        foreach ($this->channels as &$clients) {
            if (isset($clients[$conn->resourceId])) {
                unset($clients[$conn->resourceId]);
                if (empty($clients)) {
                    unset($clients);
                }
            }
        }
    }

}
