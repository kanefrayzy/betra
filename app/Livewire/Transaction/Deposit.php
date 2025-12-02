<?php

namespace App\Livewire\Transaction;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Deposit extends Component
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
            ->where('type', 'payment')
            ->where('amount', '>', 0)
            ->orderBy('updated_at', 'desc')
            ->take($this->perPage)
            ->get();

        $totalTransactions = $user->transactions()
            ->where('type', 'payment')
            ->where('amount', '>', 0)
            ->count();

        return view('livewire.transaction.deposit', [
            'transactions' => $transactions,
            'totalTransactions' => $totalTransactions,
        ]);
    }
}
