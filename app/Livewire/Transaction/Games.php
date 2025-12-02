<?php

namespace App\Livewire\Transaction;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Games extends Component
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
            ->whereIn('type', ['win', 'bet'])
            ->where('amount', '>', 0)
            ->orderBy('updated_at', 'desc')
            ->take($this->perPage)
            ->get();

        $totalTransactions = $user->transactions()
            ->whereIn('type', ['win', 'bet'])
            ->where('amount', '>', 0)
            ->count();

        return view('livewire.transaction.games', [
            'transactions' => $transactions,
            'totalTransactions' => $totalTransactions,
        ]);
    }
}
