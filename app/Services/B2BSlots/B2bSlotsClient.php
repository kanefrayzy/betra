<?php

namespace App\Services\B2BSlots;

use App\Models\GameSession;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class B2bSlotsClient
{
    protected string $baseUrl;
    protected ?int $operatorId;
    protected string $callbackUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.b2b_slots.api_url') ?? 'https://int.apichannel.cloud';
        $this->operatorId = (int) config('services.b2b_slots.partner_id');
        $this->callbackUrl = config('app.url') . '/api/callbacks/b2b';
    }

    /**
     * Генерация URL для запуска игры
     * 
     * Согласно документации B2B Slots, игры запускаются через /gamesbycode/
     * Real режим: требует auth_token для callback авторизации
     * Demo режим: без auth_token или с demo=1
     */
    public function generateGameUrl(User $user, string $gameCode, string $mode = 'real', string $currency = null): string
    {
        $params = [
            'operator_id' => $this->operatorId,
            'language' => strtolower(app()->getLocale()),
            'home_url' => route('home'),
        ];

        // Для demo режима - используем специальный user_id
        if ($mode === 'demo') {
            $params['user_id'] = 'demo';
            $params['demo_mode'] = '1';
            $params['currency'] = 'USD';
        } else {
            // Для реального режима генерируем auth_token
            $authToken = Str::random(64);
            
            // Сохраняем в кэш информацию для последующего Auth API callback
            Cache::put("b2b_auth:{$authToken}", [
                'user_id' => $user->id,
                'mode' => $mode,
                'game_code' => $gameCode,
                'currency' => $currency ?? 'USD',
            ], 3600); // 1 час

            $params['user_id'] = (string) $user->id;
            $params['auth_token'] = $authToken;
            $params['currency'] = $currency ?? 'USD';
        }

        $query = http_build_query($params);

        // URL для запуска игры
        return "{$this->baseUrl}/gamesbycode/{$gameCode}.gamecode?{$query}";
    }

    /**
     * Обработка Auth API (согласно документации 3.2, стр. 7-8)
     * Входные параметры: user_id, user_ip, user_auth_token, currency, game_code, game_name
     */
    public function handleAuth(array $data): array
    {
        try {
            // Проверяем auth_token
            $authData = Cache::get("b2b_auth:{$data['user_auth_token']}");
            if (!$authData) {
                return $this->errorResponse('do-auth-user-ingame', 4, 'Token not found');
            }

            // Находим пользователя
            $user = User::find($authData['user_id']);
            if (!$user) {
                return $this->errorResponse('do-auth-user-ingame', 5, 'User not found');
            }

            if ($user->status === 'blocked') {
                return $this->errorResponse('do-auth-user-ingame', 6, 'User blocked');
            }

            // Генерируем game_token для этой игровой сессии
            $gameToken = Str::random(64);

            // Сохраняем game_token в кэш
            Cache::put("b2b_game:{$gameToken}", [
                'user_id' => $user->id,
                'game_code' => $data['game_code'],
                'currency' => $data['currency'],
            ], 3600);

            // Создаем игровую сессию
            GameSession::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'game_code' => $data['game_code'],
                    'ended_at' => null,
                ],
                [
                    'token' => $gameToken,
                    'provider' => 'B2B Slots',
                    'aggregator' => 'b2b',
                    'mode' => $authData['mode'] ?? 'real',
                    'started_at' => now(),
                ]
            );

            return [
                'api' => 'do-auth-user-ingame',
                'success' => true,
                'answer' => [
                    'operator_id' => (int) $this->operatorId,
                    'user_id' => (string) $user->id,
                    'user_nickname' => $user->username ?? 'Player' . $user->id,
                    'balance' => number_format($user->balance, 5, '.', ''),
                    'bonus_balance' => '0.00000',
                    'auth_token' => $data['user_auth_token'],
                    'game_token' => $gameToken,
                    'error_code' => 0,
                    'error_description' => 'ok',
                    'currency' => $data['currency'],
                    'timestamp' => (string) (Carbon::now()->timestamp * 1000),
                ]
            ];

        } catch (Exception $e) {
            Log::error('B2B Auth error', ['error' => $e->getMessage()]);
            return $this->errorResponse('do-auth-user-ingame', 1, 'General error: ' . $e->getMessage());
        }
    }

    /**
     * Обработка Debit API (ставка) - согласно документации 3.3, стр. 9-10
     * Входные: user_id, user_ip, user_game_token, currency, turn_id, transaction_id,
     *          game_code, game_name, debit_amount, debit_type, round_id
     */
    public function handleDebit(array $data): array
    {
        try {
            // Проверяем game_token
            $gameData = Cache::get("b2b_game:{$data['user_game_token']}");
            if (!$gameData) {
                return $this->errorResponse('do-debit-user-ingame', 4, 'Game token not found');
            }

            $user = User::find($gameData['user_id']);
            if (!$user) {
                return $this->errorResponse('do-debit-user-ingame', 5, 'User not found');
            }

            // Проверяем дублирование транзакции
            $cacheKey = "b2b_tx:{$data['transaction_id']}";
            if (Cache::has($cacheKey)) {
                $cachedResponse = Cache::get($cacheKey);
                $cachedResponse['answer']['error_description'] = 'transaction has already proceeded';
                return $cachedResponse;
            }

            // Специальный случай: getBalance (debit_amount = 0)
            if ($data['debit_type'] === 'getBalance' || floatval($data['debit_amount']) == 0) {
                $response = [
                    'api' => 'do-debit-user-ingame',
                    'success' => true,
                    'answer' => [
                        'operator_id' => (int) $this->operatorId,
                        'transaction_id' => $data['transaction_id'],
                        'user_id' => (string) $user->id,
                        'user_nickname' => $user->username ?? 'Player' . $user->id,
                        'balance' => number_format($user->balance, 5, '.', ''),
                        'bonus_balance' => '0.00000',
                        'bonus_amount' => '0.00000',
                        'game_token' => $data['user_game_token'],
                        'error_code' => 0,
                        'error_description' => 'ok',
                        'currency' => $data['currency'],
                        'timestamp' => (string) (Carbon::now()->timestamp * 1000),
                    ]
                ];
                Cache::put($cacheKey, $response, 3600);
                return $response;
            }

            $debitAmount = floatval($data['debit_amount']);

            // Проверяем баланс
            if ($user->balance < $debitAmount) {
                return $this->errorResponse('do-debit-user-ingame', 3, 'Insufficient funds');
            }

            // Списываем средства
            $user->decrement('balance', $debitAmount);

            // Логируем транзакцию
            Log::info('B2B Debit', [
                'user_id' => $user->id,
                'transaction_id' => $data['transaction_id'],
                'amount' => $debitAmount,
                'balance_after' => $user->balance,
            ]);

            $response = [
                'api' => 'do-debit-user-ingame',
                'success' => true,
                'answer' => [
                    'operator_id' => (int) $this->operatorId,
                    'transaction_id' => $data['transaction_id'],
                    'user_id' => (string) $user->id,
                    'user_nickname' => $user->username ?? 'Player' . $user->id,
                    'balance' => number_format($user->balance, 5, '.', ''),
                    'bonus_balance' => '0.00000',
                    'bonus_amount' => '0.00000',
                    'game_token' => $data['user_game_token'],
                    'error_code' => 0,
                    'error_description' => 'ok',
                    'currency' => $data['currency'],
                    'timestamp' => (string) (Carbon::now()->timestamp * 1000),
                ]
            ];

            // Кэшируем ответ для защиты от дублирования
            Cache::put($cacheKey, $response, 3600);

            return $response;

        } catch (Exception $e) {
            Log::error('B2B Debit error', ['error' => $e->getMessage(), 'data' => $data]);
            return $this->errorResponse('do-debit-user-ingame', 1, 'General error: ' . $e->getMessage());
        }
    }

    /**
     * Обработка Credit API (выигрыш) - согласно документации 3.4, стр. 11-13
     * Входные: user_id, user_ip, user_game_token, currency, turn_id, transaction_id,
     *          game_code, game_name, credit_amount, credit_type, round_id
     */
    public function handleCredit(array $data): array
    {
        try {
            // Проверяем game_token
            $gameData = Cache::get("b2b_game:{$data['user_game_token']}");
            if (!$gameData) {
                return $this->errorResponse('do-credit-user-ingame', 4, 'Game token not found');
            }

            $user = User::find($gameData['user_id']);
            if (!$user) {
                return $this->errorResponse('do-credit-user-ingame', 5, 'User not found');
            }

            // Проверяем дублирование транзакции
            $cacheKey = "b2b_tx:{$data['transaction_id']}";
            if (Cache::has($cacheKey)) {
                $cachedResponse = Cache::get($cacheKey);
                $cachedResponse['answer']['error_description'] = 'transaction has already proceeded';
                return $cachedResponse;
            }

            $creditAmount = floatval($data['credit_amount']);

            // Начисляем выигрыш (может быть 0 если игрок проиграл)
            if ($creditAmount > 0) {
                $user->increment('balance', $creditAmount);
            }

            // Логируем транзакцию
            Log::info('B2B Credit', [
                'user_id' => $user->id,
                'transaction_id' => $data['transaction_id'],
                'amount' => $creditAmount,
                'balance_after' => $user->balance,
            ]);

            $response = [
                'api' => 'do-credit-user-ingame',
                'success' => true,
                'answer' => [
                    'operator_id' => (int) $this->operatorId,
                    'transaction_id' => $data['transaction_id'],
                    'user_id' => (string) $user->id,
                    'user_nickname' => $user->username ?? 'Player' . $user->id,
                    'balance' => number_format($user->balance, 5, '.', ''),
                    'bonus_balance' => '0.00000',
                    'bonus_amount' => '0.00000',
                    'game_token' => $data['user_game_token'],
                    'error_code' => 0,
                    'error_description' => 'ok',
                    'currency' => $data['currency'],
                    'timestamp' => (string) (Carbon::now()->timestamp * 1000),
                ]
            ];

            // Кэшируем ответ
            Cache::put($cacheKey, $response, 3600);

            return $response;

        } catch (Exception $e) {
            Log::error('B2B Credit error', ['error' => $e->getMessage(), 'data' => $data]);
            return $this->errorResponse('do-credit-user-ingame', 1, 'General error: ' . $e->getMessage());
        }
    }

    /**
     * Обработка Get Features API (фриспины) - согласно документации 3.6, стр. 14-17
     */
    public function handleGetFeatures(array $data): array
    {
        try {
            // Проверяем game_token
            $gameData = Cache::get("b2b_game:{$data['user_game_token']}");
            if (!$gameData) {
                return $this->errorResponse('do-get-features-user-ingame', 4, 'Game token not found');
            }

            $user = User::find($gameData['user_id']);
            if (!$user) {
                return $this->errorResponse('do-get-features-user-ingame', 5, 'User not found');
            }

            return [
                'api' => 'do-get-features-user-ingame',
                'success' => true,
                'answer' => [
                    'operator_id' => (int) $this->operatorId,
                    'user_id' => (string) $user->id,
                    'user_nickname' => $user->username ?? 'Player' . $user->id,
                    'balance' => number_format($user->balance, 5, '.', ''),
                    'bonus_balance' => '0.00000',
                    'game_token' => $data['user_game_token'],
                    'error_code' => 0,
                    'error_description' => 'ok',
                    'currency' => $data['currency'],
                    'timestamp' => (string) (Carbon::now()->timestamp * 1000),
                    'free_rounds' => null, // TODO: Implement freespins logic
                ]
            ];

        } catch (Exception $e) {
            Log::error('B2B Get Features error', ['error' => $e->getMessage()]);
            return $this->errorResponse('do-get-features-user-ingame', 1, 'General error');
        }
    }

    /**
     * Обработка Activate Features API - согласно документации 3.7, стр. 18-20
     */
    public function handleActivateFeatures(array $data): array
    {
        try {
            $gameData = Cache::get("b2b_game:{$data['user_game_token']}");
            if (!$gameData) {
                return $this->errorResponse('do-activate-features-user-ingame', 4, 'Game token not found');
            }

            $user = User::find($gameData['user_id']);
            if (!$user) {
                return $this->errorResponse('do-activate-features-user-ingame', 5, 'User not found');
            }

            return [
                'api' => 'do-activate-features-user-ingame',
                'success' => true,
                'answer' => [
                    'operator_id' => (int) $this->operatorId,
                    'user_id' => (string) $user->id,
                    'user_nickname' => $user->username ?? 'Player' . $user->id,
                    'balance' => number_format($user->balance, 5, '.', ''),
                    'bonus_balance' => '0.00000',
                    'game_token' => $data['user_game_token'],
                    'error_code' => 0,
                    'error_description' => 'ok',
                    'currency' => $data['currency'],
                    'timestamp' => (string) (Carbon::now()->timestamp * 1000),
                ]
            ];

        } catch (Exception $e) {
            Log::error('B2B Activate Features error', ['error' => $e->getMessage()]);
            return $this->errorResponse('do-activate-features-user-ingame', 1, 'General error');
        }
    }

    /**
     * Обработка Update Features API - согласно документации 3.8, стр. 21-23
     */
    public function handleUpdateFeatures(array $data): array
    {
        try {
            $gameData = Cache::get("b2b_game:{$data['user_game_token']}");
            if (!$gameData) {
                return $this->errorResponse('do-update-features-user-ingame', 4, 'Game token not found');
            }

            $user = User::find($gameData['user_id']);
            if (!$user) {
                return $this->errorResponse('do-update-features-user-ingame', 5, 'User not found');
            }

            return [
                'api' => 'do-update-features-user-ingame',
                'success' => true,
                'answer' => [
                    'operator_id' => (int) $this->operatorId,
                    'user_id' => (string) $user->id,
                    'user_nickname' => $user->username ?? 'Player' . $user->id,
                    'balance' => number_format($user->balance, 5, '.', ''),
                    'bonus_balance' => '0.00000',
                    'game_token' => $data['user_game_token'],
                    'error_code' => 0,
                    'error_description' => 'ok',
                    'currency' => $data['currency'],
                    'timestamp' => (string) (Carbon::now()->timestamp * 1000),
                ]
            ];

        } catch (Exception $e) {
            Log::error('B2B Update Features error', ['error' => $e->getMessage()]);
            return $this->errorResponse('do-update-features-user-ingame', 1, 'General error');
        }
    }

    /**
     * Обработка End Features API - согласно документации 3.9, стр. 24-26
     */
    public function handleEndFeatures(array $data): array
    {
        try {
            $gameData = Cache::get("b2b_game:{$data['user_game_token']}");
            if (!$gameData) {
                return $this->errorResponse('do-end-features-user-ingame', 4, 'Game token not found');
            }

            $user = User::find($gameData['user_id']);
            if (!$user) {
                return $this->errorResponse('do-end-features-user-ingame', 5, 'User not found');
            }

            return [
                'api' => 'do-end-features-user-ingame',
                'success' => true,
                'answer' => [
                    'operator_id' => (int) $this->operatorId,
                    'user_id' => (string) $user->id,
                    'user_nickname' => $user->username ?? 'Player' . $user->id,
                    'balance' => number_format($user->balance, 5, '.', ''),
                    'bonus_balance' => '0.00000',
                    'game_token' => $data['user_game_token'],
                    'error_code' => 0,
                    'error_description' => 'ok',
                    'currency' => $data['currency'],
                    'timestamp' => (string) (Carbon::now()->timestamp * 1000),
                ]
            ];

        } catch (Exception $e) {
            Log::error('B2B End Features error', ['error' => $e->getMessage()]);
            return $this->errorResponse('do-end-features-user-ingame', 1, 'General error');
        }
    }

    /**
     * Получение списка игр от B2B с кешированием
     */
    public function getGames(): array
    {
        try {
            $response = $this->getGamesList();
            
            Log::info('B2B API Response', ['response' => $response]);
            
            // Проверяем формат ответа
            if (isset($response['fail']) && $response['fail']) {
                Log::warning('B2B API returned error', ['response' => $response]);
                return [];
            }

            if (!isset($response['locator']['groups'])) {
                Log::warning('B2B API returned unexpected format', ['response' => $response]);
                return [];
            }

            // Преобразуем в стандартный формат
            $games = [];
            $baseIconUrl = $response['locator']['ico_baseurl'] ?? '/game/icons/';
            
            foreach ($response['locator']['groups'] as $group) {
                if (!isset($group['games']) || !is_array($group['games'])) continue;
                
                foreach ($group['games'] as $game) {
                    // Пропускаем игры, недоступные на PC
                    if (isset($game['gm_is_pc']) && !$game['gm_is_pc']) continue;
                    
                    // Получаем числовой ID игры (gm_bk_id)
                    $gameCode = $game['gm_bk_id'] ?? $game['id'] ?? null;
                    if (!$gameCode) continue;
                    
                    // Получаем иконку (ищем 300px)
                    $imageFile = '';
                    if (isset($game['icons']) && is_array($game['icons'])) {
                        foreach ($game['icons'] as $icon) {
                            if (isset($icon['ic_name'])) {
                                $imageFile = $icon['ic_name'];
                                if (isset($icon['ic_w']) && $icon['ic_w'] == 300) break;
                            }
                        }
                    }
                    
                    $games[] = [
                        'game_code' => (string) $gameCode,
                        'name' => $game['gm_title'] ?? $game['title'] ?? 'Game',
                        'provider' => $group['gr_title'] ?? 'B2B Slots',
                        'type' => 'slot',
                        'technology' => 'html5',
                        'image' => $imageFile ? ($this->baseUrl . $baseIconUrl . $imageFile) : null,
                        'is_mobile' => $game['gm_is_mobile'] ?? true,
                        'has_freespins' => false,
                    ];
                }
            }

            Log::info('B2B Games parsed', ['count' => count($games)]);
            return $games;

        } catch (Exception $e) {
            Log::error('Failed to fetch B2B games', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Получение списка игр от B2B API
     */
    public function getGamesList(): array
    {
        try {
            $url = "{$this->baseUrl}/frontendsrv/apihandler.api";
            $cmd = json_encode([
                'api' => 'ls-games-by-operator-id-get',
                'operator_id' => (string) $this->operatorId
            ]);

            Log::info('B2B API Request', [
                'url' => $url,
                'cmd' => $cmd,
                'operator_id' => $this->operatorId
            ]);

            $httpClient = Http::timeout(30);

            // Отключаем проверку SSL для development
            if (config('app.env') !== 'production') {
                $httpClient = $httpClient->withOptions(['verify' => false]);
            }

            $response = $httpClient->get($url, ['cmd' => $cmd]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('B2B API Raw Response', ['data' => $data]);
                return $data;
            }

            Log::error('B2B API request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            throw new Exception('Failed to fetch games list: ' . $response->status());

        } catch (Exception $e) {
            Log::error('B2B Slots games list error', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Возврат ошибки (success всегда true согласно документации)
     */
    public function errorResponse(string $api, int $errorCode, string $description): array
    {
        return [
            'api' => $api,
            'success' => true,
            'answer' => [
                'operator_id' => (int) $this->operatorId,
                'error_code' => $errorCode,
                'error_description' => $description,
                'timestamp' => (string) (Carbon::now()->timestamp * 1000),
            ]
        ];
    }
}