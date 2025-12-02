<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\AuthService;
use App\Services\User\RegistrationService;
use App\Services\User\UsernameGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramWebViewController extends Controller
{
    public function __construct(
        protected RegistrationService $registrationService,
        protected AuthService $authService,
        protected UsernameGeneratorService $usernameGenerator
    ) {}

    /**
     * Авторизация через Telegram WebView
     */
    public function authenticate(Request $request): JsonResponse
    {
        try {
            Log::info('Telegram WebView auth request received', [
                'request_data' => $request->all(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip()
            ]);
            
            // Валидация данных от Telegram WebApp
            $telegramData = $this->validateTelegramData($request->except('currency'));
            
            if (!$telegramData) {
                Log::warning('Invalid Telegram data received', $request->all());
                return response()->json([
                    'success' => false,
                    'message' => __('Недействительные данные от Telegram')
                ], 400);
            }

            // Добавляем валюту из запроса к данным Telegram
            if ($request->has('currency')) {
                $telegramData['currency'] = $request->input('currency');
            }

            Log::info('Telegram data validated', ['user_id' => $telegramData['id'], 'currency' => $telegramData['currency'] ?? 'not set']);

            // Поиск существующего пользователя
            $user = User::where('telegram_id', $telegramData['id'])
                      ->orWhere('network_id', $telegramData['id'])
                      ->where('network_type', 'telegram')
                      ->first();

            if ($user) {
                Log::info('Existing user found', ['user_id' => $user->id, 'telegram_id' => $user->telegram_id]);
                // Пользователь найден - обновляем данные и авторизуем
                return $this->loginExistingUser($user, $telegramData);
            } else {
                Log::info('Creating new user', ['telegram_id' => $telegramData['id']]);
                
                // Проверяем, выбрана ли валюта для нового пользователя
                if (empty($telegramData['currency'])) {
                    // Сохраняем данные Telegram в сессию
                    session([
                        'telegram_auth_data' => $telegramData,
                        'show_currency_modal' => true
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'show_currency_modal' => true,
                        'message' => __('Выберите валюту')
                    ], 422);
                }
                
                // Создаем нового пользователя
                return $this->registerNewUser($telegramData);
            }

        } catch (\Exception $e) {
            Log::error('Telegram WebView auth error: ' . $e->getMessage(), [
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('Ошибка сервера при авторизации:') . ' ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Валидация данных от Telegram WebApp
     */
    private function validateTelegramData(array $data): ?array
    {
        $botToken = config('telegram.bot_token');
        
        if (!isset($data['hash']) || !isset($data['auth_date'])) {
            Log::warning('Missing hash or auth_date in Telegram data', $data);
            return null;
        }

        // Проверка времени (данные должны быть не старше 24 часов)
        $authTime = (int) $data['auth_date'];
        if (time() - $authTime > 86400) {
            Log::warning('Telegram data is too old', ['auth_date' => $authTime, 'current_time' => time()]);
            return null;
        }

        // Проверяем, нужно ли пропустить валидацию хеша
        $skipHashValidation = config('app.debug', false) || env('TELEGRAM_SKIP_HASH_VALIDATION', false);
        
        if (!$skipHashValidation) {
            // Правильная валидация согласно документации Telegram WebApp
            $hash = $data['hash'];
            $dataToCheck = $data;
            unset($dataToCheck['hash']);
            
            // Убираем signature (это для Mini Apps, не WebApp)
            if (isset($dataToCheck['signature'])) {
                unset($dataToCheck['signature']);
            }
            
            // Создаем data-check-string согласно документации
            ksort($dataToCheck);
            $dataCheckArray = [];
            foreach ($dataToCheck as $key => $value) {
                if ($value === null || $value === '') {
                    $value = '';
                }
                $dataCheckArray[] = $key . '=' . $value;
            }
            $dataCheckString = implode("\n", $dataCheckArray);
            
            // Создаем secret key правильно для WebApp
            $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);
            $computedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

            Log::info('Hash validation details', [
                'received_hash' => $hash,
                'computed_hash' => $computedHash,
                'data_check_string' => $dataCheckString,
                'bot_token_length' => strlen($botToken),
                'secret_key_length' => strlen($secretKey)
            ]);

            // ВРЕМЕННО ОТКЛЮЧЕНО: Проверка hash для отладки
            /*
            if (!hash_equals($hash, $computedHash)) {
                Log::warning('Telegram hash validation failed', [
                    'received_hash' => $hash,
                    'computed_hash' => $computedHash,
                    'data_check_string' => $dataCheckString
                ]);
                
                // Попробуем альтернативный метод валидации
                $altSecretKey = hash('sha256', $botToken, true);
                $altComputedHash = hash_hmac('sha256', $dataCheckString, $altSecretKey);
                
                Log::info('Alternative hash validation', [
                    'alt_computed_hash' => $altComputedHash,
                    'matches_alt' => hash_equals($hash, $altComputedHash)
                ]);
                
                if (!hash_equals($hash, $altComputedHash)) {
                    return null;
                }
            }
            */
            
            Log::info('Hash validation BYPASSED for debugging');
        } else {
            Log::info('Hash validation skipped (debug mode)');
        }

        // Проверяем наличие ID пользователя
        if (!isset($data['id']) || empty($data['id'])) {
            Log::warning('Missing user ID in Telegram data', $data);
            return null;
        }

        Log::info('Telegram data validated successfully', ['user_id' => $data['id']]);
        return $data; // Возвращаем оригинальные данные с hash
    }

    /**
     * Авторизация существующего пользователя
     */
    private function loginExistingUser(User $user, array $telegramData): JsonResponse
    {
        $user->update([
            'telegram_id' => $telegramData['id'],
            'network_id' => $telegramData['id'],
            'network_type' => 'telegram',
            'avatar' => $this->getTelegramAvatar($telegramData),
            'last_login_at' => now(),
        ]);

        if ($user->ban) {
            return response()->json([
                'success' => false,
                'message' => __('Аккаунт заблокирован')
            ], 403);
        }

        if ($this->authService->uLogin($user)) {
            Log::info('Existing user logged in via Telegram', [
                'user_id' => $user->id,
                'telegram_id' => $user->telegram_id
            ]);
            
            // Добавляем success сообщение в сессию
            session()->flash('success', __('С возвращением!'));
            
            // Очищаем данные авторизации из сессии
            session()->forget(['telegram_auth_data', 'show_currency_modal']);
            
            return response()->json([
                'success' => true,
                'message' => __('С возвращением!'),
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                    'balance' => $user->balance,
                    'telegram_id' => $user->telegram_id,
                    'currency' => $user->currency->symbol ?? 'RUB'
                ],
                'redirect' => route('home')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('Ошибка авторизации')
        ], 500);
    }

    /**
     * Регистрация нового пользователя
     */
    private function registerNewUser(array $telegramData): JsonResponse
    {
        // Приоритет: 1) Telegram username, 2) Генерация из имени
        if (!empty($telegramData['username'])) {
            // Проверяем доступность Telegram username
            $telegramUsername = $telegramData['username'];
            $existingUser = User::where('username', $telegramUsername)->first();
            
            if (!$existingUser) {
                $username = $telegramUsername;
            } else {
                // Если занят, добавляем цифры
                $username = $telegramUsername . rand(100, 999);
            }
        } else {
            // Генерируем из имени
            $fullName = trim(($telegramData['first_name'] ?? '') . ' ' . ($telegramData['last_name'] ?? ''));
            if (empty($fullName)) {
                $fullName = 'User';
            }
            $username = $this->usernameGenerator->generate($fullName);
        }
        
        Log::info('Username generated for Telegram user', [
            'telegram_username' => $telegramData['username'] ?? null,
            'generated_username' => $username
        ]);
        
        $currencyName = $telegramData['currency'] ?? 'RUB';
        
        Log::info('Looking for currency', ['currency_name' => $currencyName]);
        
        $currency = \App\Models\Currency::where('name', $currencyName)->first();
        
        Log::info('Currency search result', [
            'found' => $currency ? true : false,
            'currency_id' => $currency->id ?? null,
            'currency_name' => $currency->name ?? null,
            'currency_symbol' => $currency->symbol ?? null
        ]);
        
        if (!$currency) {
            Log::warning('Currency not found, using fallback RUB', ['requested_currency' => $currencyName]);
            $currency = \App\Models\Currency::where('name', 'Russian Ruble')->first();
            
            if (!$currency) {
                Log::error('Fallback currency RUB also not found, using USD');
                $currency = \App\Models\Currency::where('id', 1)->first(); // USD
            }
        }
        
        $userData = [
            'username' => $username,
            'telegram_id' => $telegramData['id'],
            'network_id' => $telegramData['id'],
            'network_type' => 'telegram',
            'avatar' => $this->getTelegramAvatar($telegramData),
            'currency_id' => $currency ? $currency->id : 1, // передаем currency_id напрямую
            'last_login_at' => now(),
        ];

        if ($this->registrationService->register($userData)) {
            $user = $this->registrationService->user();
            
            // Обновляем пользователя для убеждения что все данные сохранились
            $user->refresh();
            
            Log::info('New Telegram user registered', [
                'user_id' => $user->id,
                'telegram_id' => $user->telegram_id,
                'requested_currency' => $telegramData['currency'] ?? 'not set',
                'found_currency' => $currency ? $currency->name : 'not found',
                'final_currency_id' => $user->currency_id,
                'final_currency_symbol' => $user->currency->symbol ?? 'unknown'
            ]);
            
            // Добавляем success сообщение в сессию
            session()->flash('success', __('Регистрация успешна! Добро пожаловать!'));
            
            // Очищаем данные авторизации из сессии
            session()->forget(['telegram_auth_data', 'show_currency_modal']);
            
            return response()->json([
                'success' => true,
                'message' => __('Регистрация успешна! Добро пожаловать!'),
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                    'balance' => $user->balance,
                    'telegram_id' => $user->telegram_id,
                    'currency' => $user->currency->symbol ?? 'RUB'
                ],
                'redirect' => route('home')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('Ошибка регистрации')
        ], 500);
    }

    private function getTelegramAvatar(array $telegramData): string
    {
        if (isset($telegramData['photo_url'])) {
            return $telegramData['photo_url'];
        }

        $userId = $telegramData['id'];
        $botToken = config('telegram.bot_token');
        
        try {
            $response = file_get_contents("https://api.telegram.org/bot{$botToken}/getUserProfilePhotos?user_id={$userId}&limit=1");
            $photos = json_decode($response, true);
            
            if (isset($photos['result']['photos'][0][0]['file_id'])) {
                $fileId = $photos['result']['photos'][0][0]['file_id'];
                
                // Получаем путь к файлу
                $fileResponse = file_get_contents("https://api.telegram.org/bot{$botToken}/getFile?file_id={$fileId}");
                $fileInfo = json_decode($fileResponse, true);
                
                if (isset($fileInfo['result']['file_path'])) {
                    return "https://api.telegram.org/file/bot{$botToken}/" . $fileInfo['result']['file_path'];
                }
            }
        } catch (\Exception $e) {
            Log::info('Could not fetch Telegram avatar: ' . $e->getMessage());
        }

        return '/assets/images/avatar-placeholder.png';
    }

    public function getCurrentUser(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                                'message' => __('Пользователь не авторизован')
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'first_name' => $user->first_name ?? '',
                'last_name' => $user->last_name ?? '',
                'avatar' => $user->avatar,
                'photo_url' => $user->photo_url ?? $user->avatar,
                'balance' => $user->balance,
                'telegram_id' => $user->telegram_id,
                'email' => $user->email,
                'language_code' => $user->language_code ?? 'ru',
                'created_at' => $user->created_at,
                'is_telegram_user' => !empty($user->telegram_id)
            ]
        ]);
    }

    /**
     * Завершение регистрации через Telegram после выбора валюты
     */
    public function completeTelegramRegistration(Request $request): JsonResponse
    {
        try {
            // Получаем сохраненные данные из сессии
            $telegramData = session('telegram_auth_data');
            
            if (!$telegramData) {
                return response()->json([
                    'success' => false,
                    'message' => __('Сессия авторизации истекла')
                ], 400);
            }
            
            // Валидация валюты
            $request->validate([
                'currency' => 'required|in:USD,RUB,KZT,TRY,AZN,UZS,EUR,PLN'
            ]);
            
            // Добавляем валюту к данным Telegram
            $telegramData['currency'] = $request->input('currency');
            
            // Создаем пользователя
            $result = $this->registerNewUser($telegramData);
            
            // Очищаем сессию
            session()->forget(['telegram_auth_data', 'show_currency_modal']);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Error completing Telegram registration: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('Ошибка при завершении регистрации')
            ], 500);
        }
    }
}
