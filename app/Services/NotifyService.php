<?php

namespace App\Services;

use App\Contracts\NotifyServiceContract;
use App\Models\User;
use App\Notifications\Notify;

class NotifyService implements NotifyServiceContract
{
    public function send(string $event, array $data, $user = null): void
    {
        if ($user instanceof User) {
            $this->sendNotifyToUser($event, $data, $user);
        } else {
            $this->sendNotifyToAllUsers($event, $data);
        }
    }

    public function delete($user = null): void
    {
        if ($user instanceof User) {
            $this->deleteNotifyForUser($user);
        } else {
            $this->deleteNotifyForAllUsers();
        }
    }

    public function markAsRead($user = null): void
    {
        if ($user instanceof User) {
            $this->markNotifyAsReadForUser($user);
        } else {
            $this->markNotifyAsReadForAllUsers();
        }
    }

    protected function sendNotifyToUser(string $event, array $data, $user = null): void
    {
        if ($user) {
            $user->notify(new Notify($event, $data));
        }
    }

    protected function sendNotifyToAllUsers(string $event, array $data): void
    {
        User::all()->each(function ($user) use ($event, $data) {
            $this->sendNotifyToUser($event, $data, $user);
        });
    }

    protected function deleteNotifyForUser($user = null): void
    {
        if ($user) {
            $user->notify()->delete();
        }
    }

    protected function deleteNotifyForAllUsers(): void
    {
        User::all()->each(function ($user) {
            $this->deleteNotifyForUser($user);
        });
    }

    protected function markNotifyAsReadForUser($user = null): void
    {
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }
    }

    protected function markNotifyAsReadForAllUsers(): void
    {
        User::all()->each(function ($user) {
            $this->markNotifyAsReadForUser($user);
        });
    }


}
