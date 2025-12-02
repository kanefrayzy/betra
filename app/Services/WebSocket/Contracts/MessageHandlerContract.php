<?php

namespace App\Services\WebSocket\Contracts;

use Ratchet\ConnectionInterface;

interface MessageHandlerContract
{
    public function handle(ConnectionInterface $from, $data): void;
}
