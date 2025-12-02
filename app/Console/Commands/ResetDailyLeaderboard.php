<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Currency;
use App\Notifications\Notify;
use App\Services\ExchangeService;

class ResetDailyLeaderboard extends Command
{
    protected $signature = 'leaderboard:reset';
    protected $description = 'Reset daily leaderboard and award winners';

    protected $exchangeService;

    public function __construct(ExchangeService $exchangeService)
    {
        parent::__construct();
        $this->exchangeService = $exchangeService;
    }

    public function handle()
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $defaultCurrency = config('app.currency');

        // Топ 10
        $winners = DB::table('daily_leaderboard')
            ->join('users', 'daily_leaderboard.user_id', '=', 'users.id')
            ->where('daily_leaderboard.date', $yesterday)
            ->orderByDesc('daily_leaderboard.daily_oborot')
            ->limit(10)
            ->select('users.id', 'users.currency_id', 'daily_leaderboard.daily_oborot')
            ->get();

        // Зачисление
        foreach ($winners as $index => $winner) {
            $user = User::find($winner->id);
            $prizeInDefaultCurrency = $this->getPrize($index + 1);

            // Конвертируем приз в валюту пользователя
            $userCurrency = Currency::find($winner->currency_id);
            $prizeInUserCurrency = $this->exchangeService->convert(
                $prizeInDefaultCurrency,
                $defaultCurrency,
                $userCurrency->symbol
            );

            $user->increment('balance', $prizeInUserCurrency);

            $message = __('Поздравляем! Вы заняли :place место в ежедневной гонке лидеров и выиграли :prize :currency!', [
                'place' => $index + 1,
                'prize' => number_format($prizeInUserCurrency, 2),
                'currency' => $userCurrency->symbol
            ]);
            $user->notify(Notify::send('level', ['message' => $message]));

            $this->info("User {$user->id} awarded {$prizeInUserCurrency} {$userCurrency->symbol} for place " . ($index + 1));
        }

        // Рестарт
        DB::table('daily_leaderboard')
            ->where('date', $yesterday)
            ->update(['rank' => DB::raw('(SELECT COUNT(*) + 1 FROM daily_leaderboard AS d2 WHERE d2.date = daily_leaderboard.date AND d2.daily_oborot > daily_leaderboard.daily_oborot)')]);

        $this->info('Рестарт выполнен.');
    }

    private function getPrize($place)
    {
        $prizes = [
            1 => 100,
            2 => 50,
            3 => 30,
            4 => 20,
            5 => 10,
            6 => 5,
            7 => 5,
            8 => 5,
            9 => 5,
            10 => 5
        ];

        return $prizes[$place] ?? 0;
    }
}
