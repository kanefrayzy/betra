<?php

namespace App\Console;

use App\Console\Commands\AffiliateAction;
use App\Console\Commands\GetCurrencyRates;
use App\Console\Commands\ImportSlotegratorGames;
use App\Console\Commands\UpdateUserOborot;
use App\Console\Commands\WebSocketServer;
use App\Console\Commands\SendDailyBonusLink;
use App\Console\Commands\TestLogCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        GetCurrencyRates::class,
        ImportSlotegratorGames::class,
        WebSocketServer::class,
        UpdateUserOborot::class,
        AffiliateAction::class,
        SendDailyBonusLink::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

      $schedule->command('oborot:update')
          ->everyMinute()
          ->runInBackground()
          ->appendOutputTo(storage_path('logs/oborot.log'))
          ->before(function () {
              Log::info('Запуск oborot:update');
          })
          ->after(function () {
              Log::info('Завершение oborot:update');
          })
          ->onSuccess(function () {
              Log::info('oborot:update выполнена успешно');
          })
          ->onFailure(function () {
              Log::error('oborot:update завершилась с ошибкой');
          });

        $schedule->command('log:clear oborot')->daily();

        $schedule->command('rate:get')->hourly()->appendOutputTo(storage_path('logs/schedule.log'));
        $schedule->command('referral:process-bonuses')->hourly()->appendOutputTo(storage_path('logs/affiliate.log'));
        $schedule->command('bonus:send-link')->dailyAt('14:30');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
