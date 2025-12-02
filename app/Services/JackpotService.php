<?php

namespace App\Services;

use App\Models\Jackpot;
use App\Models\JackpotBet;
use App\Models\Room;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ExchangeService;
use Exception;

class JackpotService
{


    private const MAX_CONCURRENT_GAMES = 10;
    private const COMMISSION_RATE = 0.01;


    public function getGameState(string $room): array
    {
        $currentRoom = Room::where('name', $room)->firstOrFail();

        $game = Cache::remember("jackpot:current_game:{$room}", 30, function() use ($room) {
            return Jackpot::where('room', $room)
                ->where('status', '<', Jackpot::STATUS_FINISHED)
                ->latest('id')
                ->with(['bets.user'])
                ->first();
        });

        if (!$game) {
            $game = $this->createNewGame($room);
        }

        $bets = $game->bets()->with('user')->orderBy('id', 'desc')->get()->map(function($bet) {
            return [
                'id' => $bet->id,
                'user' => [
                    'id' => $bet->user->id,
                    'username' => $bet->user->username,
                    'avatar' => $bet->user->avatar
                ],
                'amount' => floatval($bet->sum),
                'color' => $bet->color,
                'from' => $bet->from,
                'to' => $bet->to,
                'created_at' => $bet->created_at->toDateTimeString()
            ];
        });

        $chances = $this->calculateChances($game);

        return [
            'game_id' => $game->game_id,
            'status' => $game->status,
            'bank' => floatval($game->price),
            'hash' => $game->hash,
            'bets' => $bets,
            'chances' => $chances,
            'room' => [
                'name' => $currentRoom->name,
                'min' => floatval($currentRoom->min),
                'max' => floatval($currentRoom->max),
                'time' => $currentRoom->time,
                'bets_limit' => $currentRoom->bets
            ]
        ];
    }

    public function placeBet(User $user, string $room, float $amount): array {
        return DB::transaction(function() use ($user, $room, $amount) {
              $room = Room::where('name', $room)->firstOrFail();

              // Получаем текущую игру
              $game = $this->getCurrentGame($room->name);

            // Проверяем, не идет ли выбор победителя
            if ($game->status === Jackpot::STATUS_FINISHED) {
              throw new Exception('Cannot place bet while winner is being selected');
            }

            // Получаем сконвертированную сумму ставки
            $betAmount = $this->validateBet($user, $room, $amount);
            $originalBalance = $user->balance;

            $game = $this->getCurrentGame($room->name);
            $lastBet = $this->getLastBet($game);

            $bet = $this->createBet($user, $game, $betAmount, $lastBet);

            $user->decrement('balance', $amount);
            if ($user->hasActiveWagering()) {
                $exchangeService = new ExchangeService();

                if ($user->currency->symbol !== 'AZN') {
                    $betAmount = $exchangeService->convert(
                        $amount,
                        $user->currency->symbol,
                        'AZN'
                    );
                }

                $user->addToWageringAmount($betAmount);
            }
            $game->increment('price', $betAmount);


            $this->createTransaction($user, 'bet', $amount, $originalBalance, [
                'game_id' => $game->game_id,
                'room' => $room->name,
                'total_bank' => $game->price,
                'chance' => ($betAmount / $game->price) * 100
            ]);

            $shouldStart = $this->shouldStartGame($game);
            if ($shouldStart) {
                $game->update(['status' => Jackpot::STATUS_PLAYING]);
            }

            $this->clearGameCache($room->name);

            return [
                'success' => true,
                'should_start' => $shouldStart,
                'bet' => $this->formatBet($bet),
                'game' => [
                    'id' => $game->game_id,
                    'bank' => $game->price,
                    'status' => $game->status
                ],
                'chances' => $this->calculateChances($game)
            ];
        });
    }

    protected function getLastBet(Jackpot $game): ?JackpotBet
    {
        return JackpotBet::where([
            'room' => $game->room,
            'game_id' => $game->game_id
        ])
        ->latest('id')
        ->first();
    }

    protected function createBet(User $user, Jackpot $game, float $amount, ?JackpotBet $lastBet): JackpotBet {
        return DB::transaction(function() use ($user, $game, $amount, $lastBet) {


            $ticketFrom = $lastBet ? $lastBet->to + 1 : 1;
            $ticketTo = $ticketFrom + floor($amount * 10);

            // Проверяем, не создал ли кто-то билеты в этом диапазоне
            $conflictingBet = JackpotBet::where('game_id', $game->game_id)
                ->where(function($query) use ($ticketFrom, $ticketTo) {
                    $query->whereBetween('from', [$ticketFrom, $ticketTo])
                          ->orWhereBetween('to', [$ticketFrom, $ticketTo]);
                })->exists();

            if ($conflictingBet) {
                throw new Exception('Ticket range conflict detected');
            }

            return JackpotBet::create([
                'room' => $game->room,
                'game_id' => $game->game_id,
                'user_id' => $user->id,
                'sum' => $amount,
                'from' => $ticketFrom,
                'to' => $ticketTo,
                'color' => $this->generateColor($user, $game)
            ]);
        });
    }

    protected function createTransaction(User $user, string $type, float $amount, float $originalBalance, array $gameData)
    {
        return Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency_id' => $user->currency_id,
            'type' => $type,
            'status' => 'success',
            'hash' => $this->generateTransactionHash($gameData['game_id'], $user->id),
            'context' => json_encode([
                'description' => ucfirst($type) . " in Jackpot Room {$gameData['room']}",
                'amount' => $amount,
                'game_id' => $gameData['game_id'],
                'room' => $gameData['room'],
                'balance_before' => $originalBalance,
                'balance_after' => $user->balance,
                'chance' => $gameData['chance'] ?? null,
                'winning_ticket' => $gameData['winning_ticket'] ?? null,
                'total_bank' => $gameData['total_bank'] ?? null,
            ])
        ]);
    }

    protected function generateTransactionHash($gameId, $userId)
    {
        return md5($gameId . $userId . uniqid());
    }

    protected function generateColor(User $user, Jackpot $game): string
    {
        // Проверяем, есть ли у пользователя уже ставка в этой игре
        $existingBet = $game->bets()
            ->where('user_id', $user->id)
            ->first();

        if ($existingBet) {
            // Если есть, используем тот же цвет
            return $existingBet->color;
        }

        // Если нет, генерируем новый уникальный цвет
        $usedColors = $game->bets()
            ->pluck('color')
            ->toArray();

        do {
            $color = str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        } while (in_array($color, $usedColors));

        return $color;
    }

    public function finishGame(string $room): array
    {
        return DB::transaction(function() use ($room) {
            $game = $this->getCurrentGame($room);

            if ($game->status !== Jackpot::STATUS_PLAYING) {
                throw new Exception('Game is not in playing state');
            }

            // Определение победителя
            $winner = $this->determineWinner($game);

            // Обновление игры
            $game->update([
                'status' => Jackpot::STATUS_FINISHED,
                'winner_id' => $winner['user']->id,
                'winner_chance' => $winner['chance'],
                'winner_ticket' => $winner['ticket'],
                'winner_sum' => $winner['prize']
            ]);

            // Зачисление выигрыша
            $winner['user']->increment('balance', $winner['prize']);

            $originalBalance = $winner['user']->balance;

            $this->createTransaction($winner['user'], 'win', $winner['prize'], $originalBalance, [
                'game_id' => $game->game_id,
                'room' => $room,
                'total_bank' => $game->price,
                'chance' => $winner['chance'],
                'winning_ticket' => $winner['ticket']
            ]);

            // Очистка кэша
            $this->clearGameCache($room);

            return [
                'winner' => [
                    'user_id' => $winner['user']->id,
                    'username' => $winner['user']->username,
                    'avatar' => $winner['user']->avatar,
                    'ticket' => $winner['ticket'],
                    'chance' => $winner['chance'],
                    'prize' => $winner['prize']
                ],
                'game' => [
                    'id' => $game->game_id,
                    'bank' => $game->price,
                    'hash' => $game->hash
                ]
            ];
        });
    }

    public function createNewGame(string $room): Jackpot
    {
        return DB::transaction(function() use ($room) {
            $lastGame = Jackpot::where('room', $room)->latest('game_id')->first();

            $game = Jackpot::create([
                'room' => $room,
                'game_id' => $lastGame ? $lastGame->game_id + 1 : 1,
                'hash' => bin2hex(random_bytes(16)),
                'status' => Jackpot::STATUS_NOT_STARTED,
                'price' => 0
            ]);

            $this->clearGameCache($room);

            return $game;
        });
    }

    protected function validateBet(User $user, Room $room, float $amount): float {
       if ($amount <= 0) {
           throw new Exception('Bet amount must be positive');
       }

       // Конвертируем в AZN и проверяем лимиты комнаты
       $betAmountAZN = $amount;
       if ($user->currency->symbol !== 'AZN') {
           $exchangeService = new ExchangeService();
           $betAmountAZN = $exchangeService->convert($amount, $user->currency->symbol, 'AZN');
       }

       if ($betAmountAZN < $room->min) {
           $minInUserCurrency = $exchangeService->convert($room->min, 'AZN', $user->currency->symbol);
           throw new Exception("Minimum bet is {$minInUserCurrency} {$user->currency->symbol}");
       }

       if ($betAmountAZN > $room->max) {
           $maxInUserCurrency = $exchangeService->convert($room->max, 'AZN', $user->currency->symbol);
           throw new Exception("Maximum bet is {$maxInUserCurrency} {$user->currency->symbol}");
       }

       if ($user->balance < $amount) {
           throw new Exception('Insufficient funds');
       }

       return $betAmountAZN;
    }

    protected function determineWinner(Jackpot $game): array {
        return DB::transaction(function() use ($game) {
            $totalTickets = $game->bets()->max('to');
            $winningTicket = mt_rand(1, $totalTickets);

            $winningBet = $game->bets()
                ->where('from', '<=', $winningTicket)
                ->where('to', '>=', $winningTicket)
                ->with('user')
                ->lockForUpdate()
                ->firstOrFail();

            $winningBet->user->refresh();
            $chance = ($winningBet->sum / $game->price) * 100;
            $commission = $game->price * self::COMMISSION_RATE;
            $prize = $game->price - $commission;

            if ($winningBet->user->currency->symbol !== 'AZN') {
                $exchangeService = new ExchangeService();
                $prize = $exchangeService->convert(
                    $prize,
                    'AZN',
                    $winningBet->user->currency->symbol
                );
            }

            return [
                'user' => $winningBet->user,
                'ticket' => $winningTicket,
                'chance' => round($chance, 2),
                'prize' => round($prize, 2)
            ];
        });
    }

    protected function getCurrentGame(string $room): Jackpot
    {
        return Jackpot::where('room', $room)
            ->where('status', '<', Jackpot::STATUS_FINISHED)
            ->latest('id')
            ->firstOrFail();
    }

    protected function shouldStartGame(Jackpot $game): bool
    {
        return $game->bets()
            ->select('user_id')
            ->distinct()
            ->count() >= 2;
    }

    protected function clearGameCache(string $room): void
    {
        Cache::forget("jackpot:current_game:{$room}");
    }

    protected function formatBets($bets): array
    {
        return $bets->map(function($bet) {
            return $this->formatBet($bet);
        })->values()->toArray();
    }

    protected function formatBet($bet): array
    {
        return [
            'id' => $bet->id,
            'user' => [
                'id' => $bet->user->id,
                'username' => $bet->user->username,
                'avatar' => $bet->user->avatar
            ],
            'amount' => $bet->sum,
            'color' => $bet->color,
            'tickets' => [
                'from' => $bet->from,
                'to' => $bet->to
            ]
        ];
    }

    protected function calculateChances(Jackpot $game): array
    {
        if ($game->price == 0) return [];

        return $game->bets()
            ->select('user_id', DB::raw('SUM(sum) as total_sum'))
            ->with('user:id,username,avatar')
            ->groupBy('user_id')
            ->get()
            ->map(function($bet) use ($game) {
                return [
                    'user_id' => $bet->user_id,
                    'username' => $bet->user->username,
                    'avatar' => $bet->user->avatar,
                    'chance' => round(($bet->total_sum / $game->price) * 100, 2)
                ];
            })
            ->toArray();
    }
}
