<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Config;
use App\Models\User;
use App\Models\Transaction;
use App\Models\GameSession;
use Exception;
use App\Traits\Hashable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\App;

class SportController extends Controller
{
    use Hashable;

    public function openGame($id)
    {
        $gameid = $id;
        $locale = App::getLocale();

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('home')->with('error', 'Please log in to play');
        }

        if (!$user->currency) {
            return redirect()->route('home')->with('error', 'User currency not set');
        }

        // Генерируем новый токен
        $sessionToken = Str::uuid()->toString();

        // Определяем зал
        $hall = $this->getHallIdByUserCurrency($user);

        if (!$hall) {
            return redirect()->route('home')->with('error', 'Unsupported currency');
        }

        $data = [
            "cmd" => "openGame",
            "hall" => $hall,
            "key" => Config::get('sport.key', "teybet@"),
            "language" => $locale,
            "continent" => "eur",
            "login" => $user->id,
            "cdnUrl" => "/",
            "domain" => "/",
            "exitUrl" => "/",
            "demo" => "0",
            "device" => "1",
            "gameId" => $gameid,
            "sessionId" => $sessionToken

        ];

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://tbs2api.dark-a.com/API/openGame/');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);

            if ($output === false) {
                throw new Exception('Curl error: ' . curl_error($ch));
            }

            curl_close($ch);
            $output = json_decode($output, true);

            if (!isset($output['content']['game']['url'])) {
                Log::error('Invalid response from game server: ' . json_encode($output));
                throw new Exception('Invalid response from game server');
            }

            $serverSessionId = $output['content']['gameRes']['sessionId'];

             // Сохраняем serverSessionId в поле sessionId пользователя
             $user->sessionId = $serverSessionId;
             $user->save();

             // Обновляем или создаем запись в game_sessions
             $user->gameSession()->updateOrCreate(
                 ['user_id' => $user->id],
                 ['token' => $serverSessionId]
             );

            Log::info('Game session created', [
                'user_id' => $user->id,
                'session_id' => $serverSessionId,
                'token' => $serverSessionId,
                'game_id' => $gameid
            ]);

            $gameUrl = $output['content']['game']['url'];


            $agent = new Agent();
            $view = $agent->isMobile() ? 'games.mobile-sport' : 'games.sport';

            return view($view, [
                'gameUrl' => $gameUrl,
                'game' => 'Sport Game',
                'exitUrl' => "/"
            ]);
        } catch (Exception $e) {
            Log::error('Failed to open game: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Failed to open game. Please try again later.');
        }
    }

    private function getHallIdByUserCurrency(User $user)
    {
        $halls = Config::get('sport.halls');

        Log::info("Sport halls configuration:", ['halls' => $halls]);

        if (!is_array($halls) || empty($halls)) {
            return null;
        }

        $currency = $user->currency->symbol ?? null;

        if ($currency && isset($halls[$currency])) {
            Log::info("Hall found for currency {$currency}: {$halls[$currency]}");
            return $halls[$currency];
        }

        Log::warning("Unknown or missing currency for user {$user->id}: {$currency}. Using default AZN hall.");
        return $halls['AZN'] ?? null;
    }

    public function callback(Request $request)
    {
        $data = $request->all();

        try {
            switch ($data['cmd']) {
                case 'getBalance':
                    $login = $data['login'];
                    $sessionId = $data['sessionId'] ?? null;
                    $response = $this->getBalance($login, $sessionId);
                    break;

                case 'writeBet':
                    $bet = $data['bet'];
                    $win = $data['win'];
                    $session = $data['sessionId'];
                    $key = $data['key'];
                    $tradeId = $data['tradeId'];
                    $response = $this->writeBet($bet, $win, $session, $key, $tradeId);
                    break;

                default:
                    throw new Exception("Unknown cmd");
            }

            return response()->json($response);
        } catch (Exception $e) {
            Log::error('Callback error: ' . $e->getMessage());
            return response()->json([
                'status' => 'fail',
                'error' => 'Internal server error'
            ], 500);
        }
    }

    public function getBalance($login)
    {
        $user = User::lockForUpdate()->find($login, ['id', 'balance', 'currency_id']);
        if (!$user) {
            return ['status' => 'fail', 'error' => 'User not found'];
        }
        return [
            "status" => "success",
            "error" => "",
            "login" => $login,
            "balance" => number_format($user->balance, 2, '.', ''),
            "currency" => $user->currency->symbol
        ];
    }

    public function writeBet($bet, $win, $session, $key, $trxid)
    {
        if ($key !== Config::get('sport.key', 'teybet@')) {
            return ['status' => 'fail', 'error' => 'Invalid key'];
        }

        return DB::transaction(function () use ($bet, $win, $session, $trxid) {
            $user = User::where('sessionId', $session)
                ->lockForUpdate()
                ->first(['id', 'balance', 'currency_id', 'sessionId']);

            if (!$user) {
                return ['status' => 'fail', 'error' => 'User not found'];
            }

            $bet = floatval($bet);
            $win = floatval($win);

            $validationResult = $this->checkTokenValidity($user, $session);
            if ($validationResult !== null) {
                Log::warning('Invalid session', [
                    'user_id' => $user->id,
                    'session' => $session,
                    'error' => $validationResult
                ]);
                return [
                    'status' => 'fail',
                    'error' => 'Invalid session'
                ];
            }

            if ($user->balance < $bet) {
                return ['status' => 'fail', 'error' => 'Insufficient balance'];
            }

            $isWin = $win - $bet > 0 || $bet == 0;
            $amount = $isWin ? $win : $bet;
            $user->balance += $isWin ? $win : -$bet;

            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'currency_id' => $user->currency_id,
                'type' => $isWin ? 'win' : 'bet',
                'status' => 'success',
                'hash' => $trxid,
                'context' => json_encode([
                    'description' => ($isWin ? 'Win' : 'Bet') . " in Sport Betting",
                    'amount' => $amount,
                    'session_token' => $session,
                    'balance_before' => $user->balance - ($isWin ? $win : -$bet),
                    'balance_after' => $user->balance,
                ])
            ]);

            return [
                "status" => "success",
                "error" => "",
                "login" => $user->id,
                "balance" => number_format($user->balance, 2, '.', ''),
                "currency" => $user->currency->symbol,
                "operationId" => $this->hash(time())
            ];
        });
    }

    protected function checkTokenValidity($user, $sessionId): ?string
    {
        if (!$user) {
            Log::error('Null user in checkTokenValidity');
            return 'Invalid user';
        }

        if (!is_string($sessionId)) {
            Log::error('Invalid sessionId type in checkTokenValidity', [
                'user_id' => $user->id,
                'sessionId_type' => gettype($sessionId),
            ]);
            return 'Invalid session id format';
        }

        $gameSession = $user->gameSession()->first();

        Log::info('Checking token validity', [
            'user_id' => $user->id,
            'provided_sessionId' => $sessionId,
            'stored_token' => $gameSession ? $gameSession->token : null,
        ]);

        if (!$gameSession) {
            return 'No game session found';
        }

        return ($gameSession->token !== $sessionId) ? 'Invalid game session' : null;
    }

    protected function createTransaction($user, $trxid, $amount, $type, $balanceBefore, $session)
    {
        Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency_id' => $user->currency_id,
            'type' => $type,
            'status' => 'success',
            'hash' => $trxid,
            'context' => json_encode([
                'description' => ucfirst($type) . " in Sport Betting",
                'amount' => $amount,
                'session_token' => $session,
                'balance_before' => $balanceBefore,
                'balance_after' => $type === 'win' ? $balanceBefore + $amount : $balanceBefore - $amount,
            ])
        ]);
    }

}
