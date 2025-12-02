<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Models\SlotegratorGame;
use App\Models\User;
use App\Models\UserGameHistory;
use App\Services\Aes\AesClient;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class AesController extends Controller
{
    protected AesClient $client;
    protected $logger;

    public function __construct(AesClient $client)
    {
        $this->client = $client;
        $this->logger = Log::channel('single');
    }

    /**
     * Запуск игры AES
     */
     public function launchGame($game)
     {
         try {
             $locale = App::getLocale();

             $game = Cache::remember("aes_game:{$game}", 3600, function () use ($game) {
                 return SlotegratorGame::where('name', $game)
                     ->where('provider_type', 'aes')
                     ->firstOrFail();
             });

             return $this->launchGameDirect($game);

         } catch (\Exception $e) {
             Log::error('AES game launch error', [
                 'game' => $game,
                 'error' => $e->getMessage()
             ]);
             return redirect()->back()->with('error', __('Недоступно'));
         }
     }

     /**
      * Прямой запуск игры (БЕЗ дополнительных запросов к БД)
      */
     public function launchGameDirect(SlotegratorGame $game)
     {
         try {
             $user = Auth::user();
             if (!$user) {
                 return redirect()->route('home')->with('error', 'Please log in to play');
             }

             $this->client = new AesClient($user->currency->symbol);
             
             // Получаем aes_user_code (кэшируется)
             $aesUserCode = $this->getOrCreateAesUser($user);
             if (!$aesUserCode) {
                 return redirect()->back()->with('error', __('Failed to create AES user'));
             }

             // Создаем запись в истории (асинхронно, не блокирует)
             $user->gamesHistory()->create([
                 'slotegrator_game_id' => $game->id,
                 'session_token' => Str::uuid()->toString(),
                 'ip' => request()->ip(),
                 'device' => (new Agent())->device(),
             ]);

             // Парсим game_code
             $gameCodeData = $this->parseGameCode($game->game_code);

             // Получаем URL игры от AES
             $response = $this->client->getGameUrl([
                 'user_code' => $aesUserCode,
                 'provider_id' => $gameCodeData['provider_id'],
                 'game_symbol' => $gameCodeData['game_symbol'],
                 'lang' => $this->client->mapLanguage(App::getLocale()),
                 'return_url' => route('home'),
                 'win_ratio' => 0,
             ]);

             if (!$this->client->isSuccess($response)) {
                 throw new Exception('Failed to get game URL: ' . $this->client->getErrorMessage($response));
             }

             $gameUrl = $response['data']['game_url'] ?? null;
             if (!$gameUrl) {
                 return redirect()->back()->with('error', __('errors.game_unavailable'));
             }

             $view = (new Agent())->isMobile() ? 'games.mobile' : 'games.play';
             return view($view, [
                 'game' => $game,
                 'gameUrl' => $gameUrl,
                 'iframe' => true
             ]);

         } catch (\Exception $e) {
             Log::error('AES game launch error', [
                 'game' => $game->name,
                 'error' => $e->getMessage()
             ]);
             return redirect()->back()->with('error', __('Недоступно'));
         }
     }

     /**
      * Запуск игры по game_code (УСТАРЕВШИЙ - использовать launchGameDirect)
      */
     public function launchGameByCode($gameCode)
     {
         try {
             // Ищем игру по game_code (может быть JSON или строка)
             $game = Cache::remember("aes_game_code:{$gameCode}", 3600, function () use ($gameCode) {
                 return SlotegratorGame::where('game_code', 'like', "%{$gameCode}%")
                     ->where('provider_type', 'aes')
                     ->firstOrFail();
             });

             return $this->launchGameDirect($game);

         } catch (\Exception $e) {
             Log::error('AES game launch by code error', [
                 'game_code' => $gameCode,
                 'error' => $e->getMessage()
             ]);
             return redirect()->back()->with('error', __('Недоступно'));
         }
     }


     /**
      * Парсинг game_code (поддерживает JSON и строковый формат)
      */
     protected function parseGameCode(string $gameCode): array
     {
         // Пробуем декодировать как JSON
         $decoded = json_decode($gameCode, true);

         if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
             // Это JSON формат
             return [
                 'provider_id' => $decoded['provider_id'] ?? 1,
                 'game_symbol' => $decoded['game_symbol'] ?? $gameCode
             ];
         }

         // Это строковый формат "aes_{providerId}_{gameSymbol}"
         $parts = explode('_', $gameCode);

         if (count($parts) >= 3 && $parts[0] === 'aes') {
             return [
                 'provider_id' => (int)$parts[1],
                 'game_symbol' => $parts[2]
             ];
         }

         // Fallback: используем как есть
         Log::warning('Could not parse game_code, using defaults', ['game_code' => $gameCode]);

         return [
             'provider_id' => 1,
             'game_symbol' => $gameCode
         ];
     }


    /**
     * Запуск демо игры (AES не поддерживает demo напрямую через Transfer Mode)
     */
    public function launchDemoGame($game)
    {
        // AES Gaming в Transfer Mode не поддерживает демо режим
        // Перенаправляем на обычный запуск или показываем сообщение
        return redirect()->back()->with('error', __('Demo mode not available for this provider'));
    }

    /**
     * Демо запуск по коду (недоступно)
     */
    public function launchDemoGameByCode($gameCode)
    {
        return redirect()->back()->with('error', __('Demo mode not available for this provider'));
    }

    /**
     * Получить или создать AES пользователя
     */
    protected function getOrCreateAesUser(User $user): ?int
    {
        try {
            // Кэшируем aes_user_code на 1 час
            return Cache::remember("aes_user_code:{$user->id}", 3600, function () use ($user) {
                // Проверяем есть ли уже сохраненный user_code
                $aesUserCode = $user->aes_user_code ?? null;

                if ($aesUserCode) {
                    // Проверяем существует ли пользователь в AES (только первый раз)
                    $response = $this->client->getUserInfo($aesUserCode);

                    if ($this->client->isSuccess($response)) {
                        return $aesUserCode;
                    }
                }

                // Создаем нового пользователя в AES
                $aesUsername = $this->sanitizeUsername($user->username . '_' . $user->id);

                $response = $this->client->createUser($aesUsername);

                if (!$this->client->isSuccess($response)) {
                    Log::error('Failed to create AES user', [
                        'user_id' => $user->id,
                        'error' => $this->client->getErrorMessage($response)
                    ]);
                    return null;
                }

                $aesUserCode = $response['data']['user_code'] ?? null;

                if (!$aesUserCode) {
                    return null;
                }

                // Сохраняем user_code в базе
                $user->aes_user_code = $aesUserCode;
                $user->save();

                Log::info('AES user created', [
                    'user_id' => $user->id,
                    'aes_user_code' => $aesUserCode
                ]);

                return $aesUserCode;
            });

        } catch (\Exception $e) {
            Log::error('Error getting/creating AES user', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Перевод баланса в AES
     */
    protected function transferBalanceToAes(User $user, int $aesUserCode): bool
    {
        try {
            // Получаем текущий баланс пользователя в нашей системе
            $ourBalance = $user->balance;

            if ($ourBalance <= 0) {
                return true; // Нечего переводить
            }

            // Получаем текущий баланс в AES
            $aesInfo = $this->client->getUserInfo($aesUserCode);

            if (!$this->client->isSuccess($aesInfo)) {
                return false;
            }

            $aesBalance = $aesInfo['data']['balance'] ?? 0;

            // Если в AES уже есть баланс, не переводим повторно
            if ($aesBalance > 0) {
                Log::info('AES user already has balance', [
                    'user_id' => $user->id,
                    'aes_balance' => $aesBalance
                ]);
                return true;
            }

            // Переводим баланс в AES
            $depositResponse = $this->client->depositBalance($aesUserCode, $ourBalance);

            if (!$this->client->isSuccess($depositResponse)) {
                Log::error('Failed to deposit balance to AES', [
                    'user_id' => $user->id,
                    'amount' => $ourBalance,
                    'error' => $this->client->getErrorMessage($depositResponse)
                ]);
                return false;
            }

            // ВАЖНО: Здесь НЕ списываем баланс из нашей БД
            // Баланс будет возвращен при выходе из игры через withdrawAllBalance

            Log::info('Balance transferred to AES', [
                'user_id' => $user->id,
                'amount' => $ourBalance
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error transferring balance to AES', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Возврат баланса из AES
     */
    public function withdrawBalanceFromAes(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->aes_user_code) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found or not registered in AES'
                ]);
            }

            // Выводим весь баланс из AES
            $response = $this->client->withdrawAllBalance($user->aes_user_code);

            if (!$this->client->isSuccess($response)) {
                return response()->json([
                    'success' => false,
                    'message' => $this->client->getErrorMessage($response)
                ]);
            }

            $withdrawnAmount = $response['data']['amount'] ?? 0;
            $newAesBalance = $response['data']['balance'] ?? 0;

            Log::info('Balance withdrawn from AES', [
                'user_id' => $user->id,
                'withdrawn' => $withdrawnAmount,
                'aes_balance_after' => $newAesBalance
            ]);

            // Обновляем баланс в нашей системе
            // ВАЖНО: Здесь логика зависит от вашей бизнес-модели
            // Возможно нужно обновить баланс пользователя

            return response()->json([
                'success' => true,
                'withdrawn_amount' => $withdrawnAmount,
                'new_balance' => $user->balance
            ]);

        } catch (\Exception $e) {
            Log::error('Error withdrawing balance from AES', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to withdraw balance'
            ]);
        }
    }

    /**
     * Синхронизация баланса с AES
     */
    public function syncBalance(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->aes_user_code) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            // Получаем баланс из AES
            $response = $this->client->getUserInfo($user->aes_user_code);

            if (!$this->client->isSuccess($response)) {
                return response()->json([
                    'success' => false,
                    'message' => $this->client->getErrorMessage($response)
                ]);
            }

            $aesBalance = $response['data']['balance'] ?? 0;

            return response()->json([
                'success' => true,
                'our_balance' => $user->balance,
                'aes_balance' => $aesBalance
            ]);

        } catch (\Exception $e) {
            Log::error('Error syncing balance with AES', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync balance'
            ]);
        }
    }

    /**
     * Очистка имени пользователя для AES
     */
    protected function sanitizeUsername(string $username): string
    {
        // AES принимает только буквы, цифры и подчеркивание
        $username = preg_replace('/[^a-zA-Z0-9_]/', '_', $username);

        // Ограничение длины (2-50 символов)
        if (strlen($username) < 2) {
            $username = 'user_' . $username;
        }

        if (strlen($username) > 50) {
            $username = substr($username, 0, 50);
        }

        return $username;
    }

    /**
     * Получение информации об агенте
     */
    public function getAgentInfo()
    {
        try {
            $response = $this->client->getAgentInfo();

            if (!$this->client->isSuccess($response)) {
                return response()->json([
                    'success' => false,
                    'message' => $this->client->getErrorMessage($response)
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $response['data']
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting AES agent info', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get agent info'
            ]);
        }
    }

    /**
     * Получение списка активных игр
     */
    public function getOnlineGames()
    {
        try {
            $response = $this->client->getOnlineGames();

            if (!$this->client->isSuccess($response)) {
                return response()->json([
                    'success' => false,
                    'message' => $this->client->getErrorMessage($response)
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $response['data']
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting AES online games', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get online games'
            ]);
        }
    }

    /**
     * Получение транзакций
     */
    public function getTransactions(Request $request)
    {
        try {
            $startTime = $request->input('start_time');
            $endTime = $request->input('end_time');
            $offset = $request->input('offset', 0);
            $limit = $request->input('limit', 100);

            $response = $this->client->getTransactions($startTime, $endTime, $offset, $limit);

            if (!$this->client->isSuccess($response)) {
                return response()->json([
                    'success' => false,
                    'message' => $this->client->getErrorMessage($response)
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $response['data']
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting AES transactions', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get transactions'
            ]);
        }
    }
}
