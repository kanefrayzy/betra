<?php

namespace App\Livewire\Transaction;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Others extends Component
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
            ->whereIn('type', ['rain', 'bonus', 'dailybonus'])
            ->where('amount', '>', 0)
            ->orderBy('updated_at', 'desc')
            ->take($this->perPage)
            ->get();

        $totalTransactions = $user->transactions()
            ->whereIn('type', ['rain', 'bonus', 'dailybonus'])
            ->where('amount', '>', 0)
            ->count();

        return view('livewire.transaction.others', [
            'transactions' => $transactions,
            'totalTransactions' => $totalTransactions,
        ]);
    }
}
