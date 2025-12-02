<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class Notify extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $event;
    protected array $data;
    protected ?User $user;


    public function __construct(string $event, array $data, User $user = null)
    {
        $this->event = $event;
        $this->data = $data;
        $this->user = $user;
    }

    public static function send(string $event, array $data, User $user = null): static
    {
        return new static($event, $data, $user);
    }


    public function via($notifiable): array
    {
        return ['database'];
    }


    public function toArray($notifiable): array
    {
        $notificationData = [
            'event' => $this->event,
            'data' => $this->data,
        ];

        if ($this->user) {
            $notificationData['user_id'] = $this->user->id;
        }

        return $notificationData;
    }
}

