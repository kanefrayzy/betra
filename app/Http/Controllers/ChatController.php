<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;
use App\Models\Settings;
use App\Models\ForbiddenWord;
use App\Models\Transaction;
use App\Enums\TransactionType;
use App\Enums\TransactionStatus;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
    /**
     * Получить сообщения чата
     */
    public function getMessages(Request $request)
    {
        try {
            $channel = $request->input('channel', 'general');
        } catch (ValidationException $e) {
            return response()->json(['message' => __('Канала не существует')], 400);
        }

        $settings = Settings::first();

        // Проверка статуса чата
        if ($settings && $settings->chat_status >= 1) {
            return response()->json([[
                'id' => 1000,
                'user_id' => 1728,
                'username' => __('Sistem'),
                'avatar' => '/assets/images/avatars/1_1721061015.png',
                'rank_picture' => '/storage/ranks/default.png',
                'message' => $settings->chat_mess_support,
                'created_at' => Carbon::now(),
                'is_moder' => false,
                'room' => $channel
            ]]);
        }

        $messages = Message::where('room', $channel)
            ->with(['user' => function ($query) {
                $query->select('id', 'username', 'avatar', 'rank_id', 'is_admin', 'is_moder', 'is_chat_moder')
                      ->with('rank:id,name,picture');
            }])
            ->select('id', 'user_id', 'message', 'created_at', 'room')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($messages->map(function($message) {
            return [
                'id' => $message->id,
                'user_id' => $message->user->id,
                'username' => $message->user->username,
                'avatar' => $message->user->avatar,
                'rank' => $message->user->rank->name ?? 'Unknown',
                'rank_picture' => $message->user->rank->picture ? '/storage/' . $message->user->rank->picture : '',
                'message' => $message->message,
                'created_at' => $message->created_at->toDateTimeString(),
                'is_moder' => $message->user->is_admin || $message->user->is_moder || $message->user->is_chat_moder,
                'room' => $message->room
            ];
        }));
    }

    /**
     * Отправить сообщение
     */
    public function sendMessage(Request $request)
    {
        $settings = Settings::first();

        if ($settings && $settings->chat_status >= 1) {
            return response()->json(['message' => $settings->chat_mess_support], 429);
        }

        $request->validate([
            'message' => 'required|string|max:160',
            'channel' => 'required|string'
        ]);

        $user = Auth::user();
        $messageContent = mb_substr($request->input('message'), 0, 160);
        $channel = $request->input('channel');

        // Проверка бана
        if ($user->banned_until && Carbon::parse($user->banned_until)->isFuture()) {
            return response()->json([
                'message' => __('Вы забанены до :date', [
                    'date' => Carbon::parse($user->banned_until)->format('d.m.Y H:i:s')
                ])
            ], 429);
        }

        // Фильтрация сообщения
        $filterResult = $this->filterMessage(new Request(['message' => $messageContent]));
        if ($filterResult->getStatusCode() !== 200) {
            return $filterResult;
        }

        $filteredData = $filterResult->getData(true);
        $messageContent = $filteredData['filteredMessage'];

        // Создание сообщения
        $message = Message::create([
            'user_id' => $user->id,
            'username' => $user->username,
            'message' => $messageContent,
            'room' => $channel
        ]);

        return response()->json([
            'id' => $message->id,
            'user_id' => $user->id,
            'username' => $user->username,
            'avatar' => $user->avatar,
            'message' => $message->message,
            'rank' => $user->rank->name,
            'rank_picture' => '/storage/' . $user->rank->picture,
            'is_moder' => $user->is_admin || $user->is_moder || $user->is_chat_moder,
            'room' => $message->room,
        ]);
    }

    /**
     * Фильтрация сообщения
     */
    public function filterMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $request->input('message');

        // Получаем запрещенные слова и ссылки
        $forbiddenWords = ForbiddenWord::where('type', 'word')->pluck('word')->toArray();
        $forbiddenLinks = ForbiddenWord::where('type', 'link')->pluck('word')->toArray();

        // Заменяем запрещенные слова
        foreach ($forbiddenWords as $word) {
            $pattern = '/' . preg_quote($word, '/') . '/i';
            $replacement = str_repeat('*', mb_strlen($word));
            $message = preg_replace($pattern, $replacement, $message);
        }

        // Заменяем запрещенные ссылки
        foreach ($forbiddenLinks as $link) {
            $pattern = '/' . preg_quote($link, '/') . '/i';
            $replacement = str_repeat('*', mb_strlen($link));
            $message = preg_replace($pattern, $replacement, $message);
        }

        // Фильтруем все ссылки кроме разрешенных
        $message = preg_replace_callback(
            '/\bhttps?:\/\/[^\s]+/i',
            function ($matches) {
                $allowedDomains = ['flashgame.live', 'flashgame.live'];
                foreach ($allowedDomains as $domain) {
                    if (stripos($matches[0], $domain) !== false) {
                        return $matches[0];
                    }
                }
                return str_repeat('*', strlen($matches[0]));
            },
            $message
        );

        return response()->json([
            'message' => 'Message is valid',
            'filteredMessage' => $message
        ]);
    }

    /**
     * Удалить сообщение
     */
    public function deleteMessage($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        $user = Auth::user();

        // Проверка прав на удаление
        if ($user->is_moder || $user->is_admin || $user->is_chat_moder || $message->user_id == $user->id) {
            $message->delete();
            return response()->json(['message' => 'Message deleted successfully'], 200);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    /**
     * Получить информацию о пользователе
     */
    public function getUserInfo($id)
    {
        $cacheKey = "user_info_{$id}";

        $user = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($id) {
            return User::with(['rank', 'currency'])->find($id);
        });

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $currentUser = Auth::user();

        // Получаем статистику игрока из полей БД (обновляется командой oborot:update)
        $totalGames = $user->total_games ?? 0;
        $totalWins = $user->total_wins ?? 0;

        // Оборот хранится в USD (дефолтная валюта), конвертируем в валюту смотрящего
        $defaultCurrency = config('app.currency');
        $viewerCurrency = $currentUser->currency->symbol;
        
        // Если валюта смотрящего отличается от дефолтной, конвертируем
        if ($viewerCurrency !== $defaultCurrency) {
            $turnover = toUSD($user->oborot, $viewerCurrency);
        } else {
            $turnover = $user->oborot;
        }

        return response()->json([
            'username' => $user->username,
            'profile_image' => $user->avatar,
            'turnover' => moneyFormat($turnover),
            'mycurrency' => $viewerCurrency,
            'rank' => $user->rank->name,
            'rank_picture' => $user->rank ? '/storage/' . $user->rank->picture : null,
            'total_wins' => $totalWins,
            'total_games' => $totalGames,
            'is_online' => true
        ]);
    }

    /**
     * Бан пользователя
     */
    public function banUser($id, Request $request)
    {
        $request->validate([
            'reason' => 'required|integer|in:1,2,3,4',
            'duration' => 'required|in:60,1440,-1'
        ]);

        $reason = $request->input('reason');
        $duration = $request->input('duration');
        $user = User::findOrFail($id);

        $reasons = [
            1 => __('Просит деньги'),
            2 => __('Просит дождь'),
            3 => __('Нарушение правил'),
            4 => __('Спам')
        ];

        $currentUser = Auth::user();

        // Определяем длительность бана
        if ($duration == -1) {
            $user->banned_until = now()->addDays(1200);
            $durationText = __('навсегда');
        } else {
            $user->banned_until = now()->addMinutes($duration);
            $durationText = $duration == 60 ? __('1 час') : __('1 день');
        }

        $user->save();

        // Записываем информацию о бане
        $user->bans()->create([
            'reason' => $reason,
            'banned_by' => $currentUser->id,
            'duration' => $duration
        ]);

        return response()->json([
            'message' => 'User banned successfully',
            'username' => $user->username,
            'banDuration' => $durationText
        ]);
    }

    /**
     * Поделиться выигрышем в чате
     */
    public function shareWinning(Request $request)
    {
        $request->validate([
            'winning_id' => 'required|integer',
        ]);

        $winningId = $request->input('winning_id');
        $user = Auth::user();

        $winning = Transaction::where('user_id', $user->id)
            ->where('id', $winningId)
            ->where('type', TransactionType::Win)
            ->first();

        if (!$winning) {
            return response()->json(['message' => __('Выигрыш не найден')], 404);
        }

        $messageText = __('Посмотри мой выигрыш #:id!', ['id' => $winningId]);

        $message = Message::create([
            'user_id' => $user->id,
            'username' => $user->username,
            'message' => $messageText,
            'room' => 'general'
        ]);

        return response()->json([
            'id' => $message->id,
            'user_id' => $user->id,
            'username' => $user->username,
            'avatar' => $user->avatar,
            'rank' => $user->rank->name ?? 'Unknown',
            'rank_picture' => '/storage/' . $user->rank->picture ?? '',
            'message' => $messageText,
            'created_at' => $message->created_at->toDateTimeString(),
            'is_winning_share' => true,
            'winning_id' => $winningId,
        ]);
    }

    /**
     * Получить информацию о выигрыше
     */
    public function getWinningInfo($id)
    {
        $cacheKey = "winning_info_{$id}";

        $winningInfo = Cache::remember($cacheKey, now()->addWeek(), function() use ($id) {
            $winning = Transaction::where('id', $id)
                ->where('type', TransactionType::Win)
                ->first();

            if (!$winning) {
                return ['message' => __('Выигрыш не найден'), 'status' => 404];
            }

            $bet = Transaction::where('user_id', $winning->user_id)
                ->where('type', TransactionType::Bet)
                ->where('created_at', '<=', $winning->created_at)
                ->orderBy('created_at', 'desc')
                ->first();

            $user = User::where('id', $winning->user_id)->first();

            $winningContext = is_string($winning->context)
                ? json_decode($winning->context, true)
                : $winning->context;

            $betContext = $bet && is_string($bet->context)
                ? json_decode($bet->context, true)
                : ($bet->context ?? null);

            $context = is_array($betContext) ? $betContext : json_decode($betContext, true);
            $description = $context['description'] ?? 'N/A';

            if (preg_match('/in game (.+)/', $description, $matches)) {
                $gameName = $matches[1];
            } else {
                $gameName = $description;
            }

            $coefficient = $bet ? moneyFormat($winning->amount / abs($bet->amount)) : 'N/A';

            return [
                'id' => $winning->id,
                'username' => $user->username,
                'win_amount' => moneyFormat($winning->amount),
                'bet_amount' => $bet ? moneyFormat(abs($bet->amount)) : 'N/A',
                'currency' => $winning->currency->symbol,
                'date' => $winning->created_at->format('d.m.Y H:i:s'),
                'description' => $winningContext['description'] ?? 'N/A',
                'game' => $gameName ?? 'N/A',
                'hash' => $winning->hash,
                'coefficient' => $coefficient
            ];
        });

        if (isset($winningInfo['status']) && $winningInfo['status'] === 404) {
            return response()->json($winningInfo, 404);
        }

        return response()->json($winningInfo);
    }
}
