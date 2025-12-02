<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\Notify;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMassNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $userId;

    public function __construct($message, $userId = null)
    {
        $this->message = $message;
        $this->userId = $userId;
    }

    public function handle()
    {
        if ($this->userId) {
            // Отправить уведомление конкретному пользователю
            $user = User::find($this->userId);
            if ($user) {
                $user->notify(new Notify('new', [
                    'event' => 'new',
                    'message' => __($this->message)
                ]));
            }
        } else {
            // Отправить уведомление всем пользователям
            User::chunk(500, function ($users) {
                foreach ($users as $user) {
                    $user->notify(new Notify('new', [
                        'event' => 'new',
                        'message' => __($this->message)
                    ]));
                }
            });
        }
    }
}
