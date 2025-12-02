<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index()
    {
        return Redirect::to(route('transaction.deposit'));
//        $user = Auth::user();
//        $transactions = $user->transactions()
//            ->latest()
//            ->limit(10)
//            ->get();
//
//
//        return view('transaction.index', [
//            'transactions' => $transactions,
//        ]);
    }

    // public function getLatestTransactions(Request $request)
    // {
    //     $lastUpdate = $request->query('last_update', 0);
    //     $userId = auth()->id();
    //
    //     $transactions = Cache::remember('latest_transactions', 3, function () use ($lastUpdate) {
    //         return $this->getTransactions($lastUpdate);
    //     });
    //
    //     $myTransactions = Cache::remember('my_transactions_' . $userId, 3, function () use ($lastUpdate, $userId) {
    //         return $this->getTransactions($lastUpdate, $userId);
    //     });
    //
    //     return response()->json([
    //         'transactions' => $this->processTransactions($transactions),
    //         'myTransactions' => $this->processTransactions($myTransactions),
    //         'current_time' => time()
    //     ]);
    // }
    //
    // private function getTransactions($lastUpdate, $userId = null)
    // {
    //     $query = DB::table('transactions as bet')
    //         ->select(
    //             'bet.id',
    //             'users.username',
    //             'users.avatar',
    //             'bet.amount as bet_amount',
    //             'win.amount as win_amount',
    //             DB::raw('CASE WHEN bet.amount > 0 THEN win.amount / bet.amount ELSE 0 END as coefficient'),
    //             'bet.status',
    //             'bet.created_at',
    //             'currencies.symbol as currency_symbol',
    //             'bet.context'
    //         )
    //         ->join('users', 'bet.user_id', '=', 'users.id')
    //         ->join('currencies', 'bet.currency_id', '=', 'currencies.id')
    //         ->join(DB::raw('(SELECT user_id, MIN(id) as min_win_id
    //                     FROM transactions
    //                     WHERE type = "win"
    //                     GROUP BY user_id) as min_win'), function ($join) {
    //             $join->on('min_win.user_id', '=', 'bet.user_id')
    //                 ->whereRaw('min_win.min_win_id > bet.id');
    //         })
    //         ->join('transactions as win', function ($join) {
    //             $join->on('win.id', '=', 'min_win.min_win_id')
    //                 ->where('win.type', '=', 'win');
    //         })
    //         ->where('bet.type', '=', 'bet')
    //         ->where('bet.created_at', '>', date('Y-m-d H:i:s', $lastUpdate))
    //         ->orderBy('bet.user_id')
    //         ->orderByRaw('FIELD(bet.type, "bet", "win")')
    //         ->orderBy('bet.created_at', 'desc')
    //         ->limit(10);
    //
    //     if ($userId !== null) {
    //         $query->where('bet.user_id', '=', $userId);
    //     }
    //
    //     return $query->get();
    // }

    // private function processTransactions($transactions)
    // {
    //     return $transactions->map(function ($transaction) {
    //         $decodedContext = json_decode($transaction->context, true);
    //         if (json_last_error() !== JSON_ERROR_NONE) {
    //             Log::error('JSON decode error: ' . json_last_error_msg());
    //         }
    //
    //         $context = json_decode($decodedContext, true);
    //         if (json_last_error() !== JSON_ERROR_NONE) {
    //             Log::error('JSON decode error (second): ' . json_last_error_msg());
    //         }
    //
    //         $description = $context['description'] ?? 'N/A';
    //
    //         if (preg_match('/in game (.+)/', $description, $matches)) {
    //             $transaction->game_name = $matches[1];
    //         } else {
    //             $transaction->game_name = $description;
    //         }
    //
    //         unset($transaction->context);
    //         return $transaction;
    //     });
    // }

    public function deposit()
    {
        return view('transaction.deposit');
    }

    public function withdrawal()
    {
        return view('transaction.withdrawal');
    }

    public function games()
    {
        return view('transaction.games');
    }

    public function others()
    {
        return view('transaction.others');
    }
}
