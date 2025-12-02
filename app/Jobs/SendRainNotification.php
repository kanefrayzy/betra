<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\Notify;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendRainNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $senderUsername;
    protected $amount;

    public function __construct(User $user, string $senderUsername, float $amount)
    {
        $this->user = $user;
        $this->senderUsername = $senderUsername;
        $this->amount = $amount;
    }

    public function handle()
    {
        $messageNotify = __('Вы попали под дождь от :username. Вам начислено :amount :currency', [
            'username' => $this->senderUsername,
            'amount' => moneyFormat($this->amount),
            'currency' => $this->user->currency->symbol
        ]);

        $this->user->notify(Notify::send('rain', ['message' => $messageNotify]));
    }
}
