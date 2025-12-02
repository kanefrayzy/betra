<?php

namespace App\Contracts;


interface NotifyServiceContract
{
    public function send(string $event, array $data, $user = null): void;

    public function delete($user = null): void;

    public function markAsRead($user = null): void;

}
