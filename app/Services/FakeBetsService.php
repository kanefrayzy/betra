<?php

namespace App\Services;

use App\Models\Jackpot;
use App\Models\JackpotBet;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FakeBetsService
{
    protected $logger;

    public function __construct()
    {
        $this->logger = Log::channel('fake-bets');
    }

    public function placeFakeBet(string $room): array
    {
        try {
            $room = Room::where('name', $room)->firstOrFail();
            $game = Jackpot::where('room', $room->name)
                ->where('status', '<', Jackpot::STATUS_PRE_FINISH)
                ->latest('id')
                ->first();

            if (!$game) {
                throw new \Exception('No active game found');
            }

            // Получаем фейкового пользователя
            $fakeUser = $this->getFakeUser();

            // Генерируем сумму ставки
            $betAmount = $this->calculateBetAmount($room, $game);

            // Проверяем лимиты ставок для пользователя
            $userBetsCount = JackpotBet::where([
                'room' => $room->name,
                'game_id' => $game->game_id,
                'user_id' => $fakeUser->id
            ])->count();

            if ($userBetsCount >= $room->bets) {
                throw new \Exception('Bet limit reached for user');
            }

            // Создаем ставку
            $bet = JackpotBet::create([
                'room' => $room->name,
                'game_id' => $game->game_id,
                'user_id' => $fakeUser->id,
                'sum' => $betAmount,
                'from' => $this->calculateTicketRange($game)['from'],
                'to' => $this->calculateTicketRange($game)['to'],
                'color' => $this->generateColor(),
                'is_fake' => true
            ]);

            // Обновляем банк игры
            $game->increment('price', $betAmount);

            // Очищаем кэш
            Cache::tags(['jackpot', $room->name])->flush();

            $this->logger->info('Fake bet placed', [
                'room' => $room->name,
                'game_id' => $game->game_id,
                'user' => $fakeUser->username,
                'amount' => $betAmount
            ]);

            return [
                'success' => true,
                'bet' => $bet,
                'user' => $fakeUser,
                'game' => $game
            ];

        } catch (\Exception $e) {
            $this->logger->error('Error placing fake bet', [
                'error' => $e->getMessage(),
                'room' => $room
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    protected function getFakeUser(): User
    {
        return Cache::remember('fake_users', 3600, function () {
            return User::where('fake', true)
                ->inRandomOrder()
                ->firstOr(function () {
                    // Создаем фейковых пользователей если их нет
                    return $this->createFakeUsers();
                });
        });
    }

    protected function calculateBetAmount(Room $room, Jackpot $game): float
    {
        $lastBet = JackpotBet::where([
            'room' => $room->name,
            'game_id' => $game->game_id
        ])->latest()->first();

        if (!$lastBet) {
            // Если это первая ставка
            return round(rand($room->min * 100, $room->min * 200) / 100, 2);
        }

        // Генерируем ставку относительно последней
        $minBet = max($room->min, $lastBet->sum * 0.5);
        $maxBet = min($room->max, $lastBet->sum * 1.5);

        return round(rand($minBet * 100, $maxBet * 100) / 100, 2);
    }

    protected function calculateTicketRange(Jackpot $game): array
    {
        $lastBet = JackpotBet::where([
            'room' => $game->room,
            'game_id' => $game->game_id
        ])->latest()->first();

        $from = $lastBet ? $lastBet->to + 1 : 1;
        $to = $from + 999; // Фиксированный размер диапазона для фейковых ставок

        return [
            'from' => $from,
            'to' => $to
        ];
    }

    protected function generateColor(): string
    {
        return str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    protected function createFakeUsers(): User
    {
        $fakeUsers = [];
        $avatars = $this->getFakeAvatars();

        for ($i = 0; $i < 50; $i++) {
            $fakeUsers[] = User::create([
                'username' => 'player' . rand(1000, 9999),
                'email' => 'fake' . rand(1000, 9999) . '@example.com',
                'password' => bcrypt(str_random(20)),
                'avatar' => $avatars[array_rand($avatars)],
                'is_fake' => true,
                'balance' => rand(1000, 10000)
            ]);
        }

        return $fakeUsers[0];
    }

    protected function getFakeAvatars(): array
    {
        // Можно использовать реальные аватарки или сгенерированные
        return [
            '/avatars/default1.png',
            '/avatars/default2.png',
            '/avatars/default3.png',
            // ...добавьте больше аватарок
        ];
    }
}
