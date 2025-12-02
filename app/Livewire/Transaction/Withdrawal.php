<?php
namespace App\Livewire\Transaction;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;

class Withdrawal extends Component
{
    public $perPage = 15;

    public function loadMore()
    {
        $this->perPage += 15;
    }

    public function render()
    {
        $user = Auth::user();
        $transactions = $user->transactions()
            ->where('type', 'withdrawal')
            ->where('amount', '>', 0)
            ->orderBy('updated_at', 'desc')
            ->take($this->perPage)
            ->get()
            ->map(function ($transaction) {
                // Добавляем информацию о таймере для каждой транзакции
                $createdAt = Carbon::parse($transaction->created_at);
                $endTime = $createdAt->copy()->addMinutes(10);
                $now = Carbon::now();

                // Проверяем, нужно ли показывать таймер
                if ($transaction->status->value === 'pending' && $now->lt($endTime)) {
                    $timeLeft = $now->diffInSeconds($endTime);
                    $transaction->timeLeft = [
                        'minutes' => floor($timeLeft / 60),
                        'seconds' => $timeLeft % 60,
                        'show' => true
                    ];
                } else {
                    $transaction->timeLeft = ['show' => false];
                }

                return $transaction;
            });

        $totalTransactions = $user->transactions()
            ->where('type', 'withdrawal')
            ->where('amount', '>', 0)
            ->count();

        return view('livewire.transaction.withdrawal', [
            'transactions' => $transactions,
            'totalTransactions' => $totalTransactions,
        ]);
    }
}
