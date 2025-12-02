<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\DailyBonusController;

class SendDailyBonusLink extends Command
{
    protected $signature = 'bonus:send-link';
    protected $description = 'Generate and send daily bonus link to Telegram channel';

    public function handle(DailyBonusController $controller)
    {
        $controller->generateBonusLink();
        $this->info('Daily bonus link sent to Telegram channel.');
    }
}
