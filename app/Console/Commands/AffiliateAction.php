<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\ReferralBonus;
use App\Enums\TransactionType;
use App\Enums\TransactionStatus;
use App\Services\ExchangeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AffiliateAction extends Command
{
    protected $signature = 'referral:process-bonuses';
    protected $description = 'Process referral bonuses for users';

    protected ExchangeService $exchangeService;

    // Значение бонуса по умолчанию (20%)
    private const DEFAULT_BONUS_PERCENTAGE = 0.2;

    public function __construct(ExchangeService $exchangeService)
    {
        parent::__construct();
        $this->exchangeService = $exchangeService;
    }

    public function handle(): void
    {
        $this->info('Start processing referral bonuses...');

        $defaultCurrency = Currency::where('symbol', config('app.currency'))->firstOrFail();

        User::chunk(200, function ($users) use ($defaultCurrency) {
            foreach ($users as $user) {
                $transactions = Transaction::select('type', 'amount', 'currency_id')
                    ->where('user_id', $user->id)
                    ->whereIn('type', [
                        TransactionType::Bet->value,
                        TransactionType::Win->value,
                        TransactionType::Deposit->value,
                        TransactionType::Refund->value,
                        TransactionType::Bonus->value,
                        TransactionType::Rollback->value
                    ])
                    ->where('status', TransactionStatus::Success->value)
                    ->where('created_at', '>=', now()->subHour())
                    ->get();

                $totals = [
                    TransactionType::Bet->value => 0,
                    TransactionType::Win->value => 0,
                    TransactionType::Deposit->value => 0,
                    TransactionType::Refund->value => 0,
                    TransactionType::Bonus->value => 0,
                    TransactionType::Rollback->value => 0
                ];

                foreach ($transactions as $transaction) {
                    $amount = $this->exchangeService->toDefaultCurrency(
                        $transaction->amount,
                        $transaction->currency->symbol
                    );
                    $totals[$transaction->type->value] += $amount;
                }

                $actualLoss = $totals[TransactionType::Deposit->value]
                    - $totals[TransactionType::Bet->value]
                    + $totals[TransactionType::Win->value]
                    - $totals[TransactionType::Deposit->value]
                    + $totals[TransactionType::Refund->value]
                    + $totals[TransactionType::Bonus->value]
                    + $totals[TransactionType::Rollback->value];

                if ($actualLoss < 0) {
                    $this->processReferralBonus($user, abs($actualLoss), $defaultCurrency);
                }
            }
        });

        $this->info('Referral bonus processing completed.');
    }

    private function processReferralBonus(User $user, float $loss, Currency $defaultCurrency): void
    {
        $referrer = User::select('id', 'currency_id', 'ref_balance', 'ref_percentage')
            ->where('user_id', $user->referred_by)
            ->first();

        if (!$referrer) {
            return;
        }

        // Используем индивидуальный процент, если он установлен, иначе используем значение по умолчанию
        $bonusPercentage = $referrer->ref_percentage
            ? ($referrer->ref_percentage / 100)
            : self::DEFAULT_BONUS_PERCENTAGE;

        $refBonus = round($loss * $bonusPercentage, 2);

        $givenBonuses = ReferralBonus::where('referrer_user_id', $referrer->id)
            ->where('referral_user_id', $user->id)
            ->sum('amount');

        $currentBonus = max(0, $refBonus - $givenBonuses);

        if ($currentBonus > 0) {
            DB::transaction(function () use ($user, $referrer, $currentBonus) {
                ReferralBonus::create([
                    'referrer_user_id' => $referrer->id,
                    'referral_user_id' => $user->id,
                    'amount' => $currentBonus,
                ]);

                $referrer->increment('ref_balance', $currentBonus);
                $user->increment('from_ref', $currentBonus);
            });

            Log::info('Sent Bonus', [
                'referrer_id' => $referrer->id,
                'referral_id' => $user->id,
                'amount' => round($currentBonus, 2),
                'currency' => $defaultCurrency->symbol,
                'percentage' => $bonusPercentage * 100 . '%'
            ]);
        }
    }
}
