<?php

namespace App\Console\Commands;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Rank;
use App\Models\DailyLeaderboard;
use App\Services\ExchangeService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Notifications\Notify;

class UpdateUserOborot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oborot:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates users oborot by summing up amounts from bet transactions';

    /**
     * Execute the console command.
     */
     public function handle()
     {
         $this->info('Updating user oborot from bet transactions...');
         $defaultCurrency = config("app.currency");
         $oneMinuteAgo = Carbon::now()->subMinute();

         // Получаем bet и refund транзакции
         $transactions = Transaction::whereIn('type', [TransactionType::Bet, TransactionType::Refund])
             ->where('status', TransactionStatus::Success)
             ->where('created_at', '>=', $oneMinuteAgo)
             ->get();

         // Получаем win транзакции отдельно
         $winTransactions = Transaction::where('type', TransactionType::Win)
             ->where('status', TransactionStatus::Success)
             ->where('created_at', '>=', $oneMinuteAgo)
             ->get();

         // Группируем bet и refund транзакции
         $totals = $transactions->groupBy(['user_id', 'currency_id', 'type'])->map(function ($userTransactions) {
             return $userTransactions->map(function ($currencyTransactions) {
                 return $currencyTransactions->map(function ($typeTransactions) {
                     return $typeTransactions->sum('amount');
                 });
             });
         });

         // Группируем win транзакции
         $winTotals = $winTransactions->groupBy(['user_id', 'currency_id'])->map(function ($userTransactions) {
             return $userTransactions->map(function ($currencyTransactions) {
                 return [
                     'amount' => $currencyTransactions->sum('amount'),
                     'count' => $currencyTransactions->where('amount', '>', 0)->count()
                 ];
             });
         });

         // Load all users and currencies at once to avoid multiple queries
         $users = User::whereIn('id', $totals->keys()->merge($winTotals->keys()))->get()->keyBy('id');
         $currencies = Currency::all()->keyBy('id');

         // Обновляем ставки и оборот
         foreach ($totals as $userId => $currencyTotals) {
               $totalAmountInUsd = 0;
               $totalBetsAmountInUsd = 0;
               $gamesCount = 0;
               
               foreach ($currencyTotals as $currencyId => $typeTotals) {
                   $currency = $currencies->get($currencyId);
                   $betAmount = $typeTotals[TransactionType::Bet->value] ?? 0;
                   $refundAmount = $typeTotals[TransactionType::Refund->value] ?? 0;
                   $netAmount = $betAmount - $refundAmount;

                   // Считаем количество игр (bet транзакции)
                   $betTransactionsCount = $transactions->where('user_id', $userId)
                       ->where('currency_id', $currencyId)
                       ->where('type', TransactionType::Bet)
                       ->count();
                   $gamesCount += $betTransactionsCount;

                   if ($currency->symbol != $defaultCurrency) {
                       $amountInUsd = $this->convertToUsd($netAmount, $currency->symbol, $defaultCurrency);
                       $betsInUsd = $this->convertToUsd(abs($betAmount), $currency->symbol, $defaultCurrency);
                       $this->info("Converted to USD: $amountInUsd");
                       $totalAmountInUsd += $amountInUsd;
                       $totalBetsAmountInUsd += $betsInUsd;
                   } else {
                       $totalAmountInUsd += $netAmount;
                       $totalBetsAmountInUsd += abs($betAmount);
                   }
               }
               
             $user = $users->get($userId);
             if ($user) {
                 $user->increment('oborot', $totalAmountInUsd);
                 $user->increment('total_games', $gamesCount);
                 $user->increment('total_bets_amount', $totalBetsAmountInUsd);
                 
                 $this->updateUserRank($user);
                 $this->updateUserRakeback($user, $totalAmountInUsd);
                 // $this->updateDailyLeaderboard($user, $totalAmountInUsd);
                 $this->updateTournamentLeaderboard($user, $totalAmountInUsd);
                 $this->info("Updated user {$user->id} with amount {$totalAmountInUsd} USD, games: {$gamesCount}");
             }
         }

         // Обновляем выигрыши
         foreach ($winTotals as $userId => $currencyTotals) {
             $totalWinsAmountInUsd = 0;
             $totalWinsCount = 0;
             
             foreach ($currencyTotals as $currencyId => $winData) {
                 $currency = $currencies->get($currencyId);
                 $winAmount = $winData['amount'];
                 $winCount = $winData['count'];
                 
                 $totalWinsCount += $winCount;
                 
                 if ($currency->symbol != $defaultCurrency) {
                     $winsInUsd = $this->convertToUsd($winAmount, $currency->symbol, $defaultCurrency);
                     $totalWinsAmountInUsd += $winsInUsd;
                 } else {
                     $totalWinsAmountInUsd += $winAmount;
                 }
             }
             
             $user = $users->get($userId);
             if ($user) {
                 $user->increment('total_wins', $totalWinsCount);
                 $user->increment('total_wins_amount', $totalWinsAmountInUsd);
                 $this->info("Updated user {$user->id} wins: {$totalWinsCount}, amount: {$totalWinsAmountInUsd} USD");
             }
         }

         $this->info('All users updated successfully!');
     }

    private function convertToUsd($amount, $from, $to): float|int
    {
        $exchangeService = new ExchangeService();
        return $exchangeService->convert($amount, $from, $to);
    }

    private function updateUserRank(User $user)
    {
        $currentRank = $user->rank;
        $nextRank = Rank::where('oborot_min', '<=', $user->oborot)
            ->where('id', '>', $currentRank->id ?? 0)
            ->orderBy('id', 'asc')
            ->first();

        if ($nextRank) {
            $user->rank_id = $nextRank->id;
            $user->save();

            // Отправляем уведомление о повышении уровня
            $messageNotify = __('Поздравляем! Вы достигли :level уровня', [
                'level' => $nextRank->name
            ]);

            $user->notify(Notify::send('level', ['message' => $messageNotify]));
            $this->info("User {$user->id} promoted to rank {$nextRank->id}");
        }
    }


    private function updateUserRakeback(User $user, float $amount)
    {
        $rank = $user->rank;
        if ($rank) {
            $rakebackPercentage = $rank->rakeback / 100;
            $rakebackAmount = $amount * $rakebackPercentage;

            $user->increment('rakeback', $rakebackAmount);
            $this->info("Added rakeback {$rakebackAmount} USD to user {$user->id}");
            //  JSON context
            $context = [
                "description" => "Rakeback",
                "amount" => $rakebackAmount,
                "balance_before" => $user->balance,
                "balance_after" => $user->balance + $rakebackAmount
            ];

            Transaction::create([
                'user_id' => $user->id,
                'amount' => $rakebackAmount,
                'currency_id' => Currency::where('symbol', config('app.currency'))->first()->id,
                'type' => TransactionType::Bonus->value,
                'status' => TransactionStatus::Success->value,
                'context' => json_encode($context)
            ]);
        }
    }

    // leaderboard
    private function updateDailyLeaderboard(User $user, float $amount)
    {
        $today = Carbon::today()->toDateString();

        DB::table('daily_leaderboard')->updateOrInsert(
            ['user_id' => $user->id, 'date' => $today],
            ['daily_oborot' => DB::raw("daily_oborot + $amount")]
        );
    }

    private function updateTournamentLeaderboard(User $user, float $amount)
    {
          $activeTournament = DB::table('tournaments')
              ->where('status', 'active')
              ->where('start_date', '<=', now())
              ->where('end_date', '>=', now())
              ->first();

          if ($activeTournament) {
                    DB::table('tournament_leaderboard')->updateOrInsert(
                        [
                            'tournament_id' => $activeTournament->id,
                            'user_id' => $user->id
                        ],
                        [
                            'turnover' => DB::raw("turnover + $amount"),
                            'updated_at' => now()
                        ]
                    );
                }
    }

}
