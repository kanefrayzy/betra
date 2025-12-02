<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use App\Services\User\RegistrationService;

class TelegramController extends Controller
{
    protected $botToken;
    protected $channelId;
    protected $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
        // Убедимся, что токен не содержит префикс "bot"
        $this->botToken = trim(env('TELEGRAM_BOT_TOKEN_NEW'));

        // Если токен уже содержит префикс "bot", удалим его
        if (strpos($this->botToken, 'bot') === 0) {
            $this->botToken = substr($this->botToken, 3);
        }

        $this->channelId = env('TELEGRAM_CHANNEL_NEW');
    }

    public function connect(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('home')->with('error', __('Необходимо авторизоваться'));
        }

        $user = Auth::user();
        $token = Str::random(32);
        \Cache::put('telegram_token_' . $token, $user->id, now()->addMinutes(30));

        // Правильное формирование URL для API запроса
        $response = Http::get("https://api.telegram.org/bot{$this->botToken}/getMe");

        if (!$response->successful()) {
            Log::error('Telegram API error response: ' . $response->body());
            return redirect()->back()->with('error', __('Ошибка подключения к Telegram'));
        }

        $botInfo = $response->json();

        if (!isset($botInfo['result']) || !isset($botInfo['result']['username'])) {
            Log::error('Invalid Telegram API response structure: ' . json_encode($botInfo));
            return redirect()->back()->with('error', __('Неверный ответ от Telegram API'));
        }

        $botUsername = $botInfo['result']['username'];
        $url = "https://t.me/{$botUsername}?start={$token}";
        return redirect($url);
    }

    public function webhook(Request $request)
    {
        $update = $request->all();
        if (isset($update['message']['text'])) {
            $text = $update['message']['text'];
            
            if (strpos($text, '/start') === 0) {
                $params = substr($text, 7); // Получаем всё после /start
                
                Log::info('Received /start command', [
                    'text' => $text,
                    'params' => $params,
                    'user' => $update['message']['from']
                ]);
                
                // Проверяем, это токен авторизации (login_TOKEN или register_TOKEN)
                if (strpos($params, 'login_') === 0 || strpos($params, 'register_') === 0) {
                    Log::info('Detected auth token, calling handleAuthToken');
                    $this->handleAuthToken($update, $params);
                    return response()->json(['status' => 'ok']);
                }
                
                // Старая логика для привязки telegram к существующему аккаунту
                $token = $params;
                $userId = \Cache::get('telegram_token_' . $token);

                if ($userId) {
                    $user = User::find($userId);
                    if ($user) {
                        $telegramId = $update['message']['from']['id'];
                        // Проверяем, не привязан ли уже этот Telegram ID к другому пользователю
                        $existingUser = User::where('telegram_id', $telegramId)->first();
                        if ($existingUser && $existingUser->id !== $user->id) {
                            $this->sendTelegramMessage($update['message']['chat']['id'], __('Этот Telegram аккаунт уже привязан к другому пользователю.'));
                        } else {
                            $user->telegram_id = $telegramId;
                            $user->save();
                            \Cache::forget('telegram_token_' . $token);
                            
                            // Отправляем сообщение с inline кнопкой для открытия приложения
                            $this->sendTelegramMessageWithWebApp(
                                $update['message']['chat']['id'], 
                                __('✅ Ваш аккаунт успешно привязан!') . "\n\n" . 
                                __('Теперь подпишитесь на наш канал: ') . env('TELEGRAM_GROUP_LINK') . "\n\n" .
                                __('Откройте приложение, чтобы начать играть!')
                            );
                        }
                    } else {
                        // Log::error(__('Пользователь не найден для ID: ') . $userId);
                    }
                } else {
                    // Если токена нет, отправляем приветственное сообщение с кнопкой
                    $bonusAmount = rand(40, 200);
                    $this->sendTelegramMessageWithWebApp(
                        $update['message']['chat']['id'],
                        "🎉 " . __('Добро пожаловать!') . "\n\n" .
                        "💰 " . __('Вы получили приветственный бонус') . " <b>$" . $bonusAmount . "</b>!\n\n" .
                        "🎮 " . __('Нажмите кнопку ниже, чтобы начать играть!') . "\n\n" .
                        "━━━━━━━━━━━━━━━━━\n\n" .
                        "🎉 Xush kelibsiz!\n\n" .
                        "💰 Siz kutish bonusini oldingiz <b>$" . $bonusAmount . "</b>!\n\n" .
                        "🎮 O'ynashni boshlash uchun quyidagi tugmani bosing!"
                    );
                    // Log::warning(__('Токен не найден в кэше: ') . $token);
                }
            }
        }
        return response()->json(['status' => 'ok']);
    }

    public function checkMembership(Request $request)
    {
        $user = Auth::user();
        if (!$user->telegram_id) {
            return response()->json(['error' => __('Telegram не привязан')], 400);
        }
        $isMember = $this->isUserMember($user->telegram_id);
        return response()->json(['is_member' => $isMember]);
    }

    protected function isUserMember($userId)
    {
        $response = Http::get("https://api.telegram.org/bot{$this->botToken}/getChatMember", [
            'chat_id' => $this->channelId,
            'user_id' => $userId
        ]);
        if ($response->successful()) {
            $result = $response->json();
            return in_array($result['result']['status'], ['creator', 'administrator', 'member']);
        }
        return false;
    }

    protected function sendTelegramMessage($chatId, $text)
    {
        Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text
        ]);
    }
    
    protected function sendTelegramMessageWithWebApp($chatId, $text)
    {
        $appUrl = config('app.url'); // или укажите свой URL
        
        Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => '🎮 Получить бонус',
                            'web_app' => [
                                'url' => $appUrl
                            ]
                        ]
                    ],
                    [
                        [
                            'text' => '🎮 Bonusni olish',
                            'web_app' => [
                                'url' => $appUrl
                            ]
                        ]
                    ]
                ]
            ])
        ]);
    }
    
    /**
     * Обработка токена авторизации из /start login_TOKEN или /start register_TOKEN
     */
    protected function handleAuthToken($update, $params)
    {
        list($type, $token) = explode('_', $params, 2);
        
        Log::info('Telegram auth token received', [
            'type' => $type,
            'token' => $token,
            'params' => $params
        ]);
        
        $tokenData = \Cache::get('telegram_auth_token_' . $token);
        
        Log::info('Token data from cache', [
            'token_key' => 'telegram_auth_token_' . $token,
            'data' => $tokenData
        ]);
        
        if (!$tokenData) {
            $this->sendTelegramMessage(
                $update['message']['chat']['id'],
                __('❌ Ссылка устарела или недействительна') . "\n\n" .
                __('Пожалуйста, получите новую ссылку на сайте.')
            );
            return;
        }
        
        $telegramId = $update['message']['from']['id'];
        $telegramUsername = $update['message']['from']['username'] ?? null;
        $firstName = $update['message']['from']['first_name'] ?? '';
        $lastName = $update['message']['from']['last_name'] ?? '';
        
        $user = User::where('telegram_id', $telegramId)->first();
        
        if ($type === 'login') {
            // Попытка входа
            if ($user) {
                // Пользователь найден - сохраняем для автоматического входа
                \Cache::put('telegram_auth_success_' . $token, [
                    'user_id' => $user->id,
                    'action' => 'login'
                ], now()->addMinutes(5));
                
                \Cache::forget('telegram_auth_token_' . $token);
                
                $this->sendTelegramMessageWithWebApp(
                    $update['message']['chat']['id'],
                    __('✅ Вход выполнен успешно!') . "\n\n" .
                    __('Добро пожаловать, ') . $user->username . '!' . "\n\n" .
                    __('Вернитесь на сайт или откройте приложение.')
                );
            } else {
                // Пользователь не найден - предлагаем регистрацию
                $this->sendTelegramMessage(
                    $update['message']['chat']['id'],
                    __('❌ Ваш Telegram не привязан ни к одному аккаунту') . "\n\n" .
                    __('Пожалуйста, сначала зарегистрируйтесь на сайте.')
                );
                \Cache::forget('telegram_auth_token_' . $token);
            }
        } else {
            // Регистрация
            if ($user) {
                // Уже есть аккаунт с этим Telegram - просто логиним
                \Cache::put('telegram_auth_success_' . $token, [
                    'user_id' => $user->id,
                    'action' => 'login'
                ], now()->addMinutes(5));
                
                \Cache::forget('telegram_auth_token_' . $token);
                
                $this->sendTelegramMessageWithWebApp(
                    $update['message']['chat']['id'],
                    __('✅ У вас уже есть аккаунт!') . "\n\n" .
                    __('Выполняется вход...') . "\n\n" .
                    __('Вернитесь на сайт или откройте приложение.')
                );
            } else {
                // Новый пользователь - сохраняем данные для регистрации
                \Cache::put('telegram_auth_success_' . $token, [
                    'telegram_id' => $telegramId,
                    'telegram_username' => $telegramUsername,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'action' => 'register'
                ], now()->addMinutes(5));
                
                \Cache::forget('telegram_auth_token_' . $token);
                
                $this->sendTelegramMessage(
                    $update['message']['chat']['id'],
                    __('✅ Отлично!') . "\n\n" .
                    __('Вернитесь на сайт и завершите регистрацию, выбрав валюту.')
                );
            }
        }
    }
    
    /**
     * Генерация токена для входа/регистрации через Telegram
     */
    public function generateAuthToken(Request $request)
    {
        $type = $request->input('type', 'login'); // 'login' или 'register'
        $token = Str::random(32);
        
        \Cache::put('telegram_auth_token_' . $token, [
            'type' => $type,
            'created_at' => now(),
            'ip' => $request->ip()
        ], now()->addMinutes(10));
        
        $botUsername = config('telegram.bot_username');
        $deepLink = "https://t.me/{$botUsername}?start={$type}_{$token}";
        
        return response()->json([
            'success' => true,
            'token' => $token,
            'deep_link' => $deepLink,
            'bot_username' => $botUsername,
            'expires_in' => 600
        ]);
    }
    
    /**
     * Проверка статуса авторизации по токену
     */
    public function checkAuthStatus(Request $request)
    {
        $token = $request->input('token');
        
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token required'], 400);
        }
        
        $authData = \Cache::get('telegram_auth_success_' . $token);
        
        if (!$authData) {
            return response()->json([
                'success' => false,
                'status' => 'waiting'
            ]);
        }
        
        // Если это вход и пользователь найден - логиним его
        if ($authData['action'] === 'login' && isset($authData['user_id'])) {
            $user = \App\Models\User::find($authData['user_id']);
            if ($user) {
                \Auth::login($user);
                \Cache::forget('telegram_auth_success_' . $token);
                
                return response()->json([
                    'success' => true,
                    'status' => 'completed',
                    'action' => 'login',
                    'redirect' => route('home')
                ]);
            }
        }
        
        // Если это регистрация - возвращаем данные для выбора валюты
        return response()->json([
            'success' => true,
            'status' => 'completed',
            'data' => $authData
        ]);
    }
    
    /**
     * Завершить регистрацию через код Telegram
     */
    public function completeCodeAuth(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string',
            'telegram_id' => 'required|integer',
            'telegram_username' => 'nullable|string',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
        ]);
        
        try {
            // Проверяем, не существует ли уже пользователь с таким telegram_id
            $existingUser = \App\Models\User::where('telegram_id', $validated['telegram_id'])->first();
            
            if ($existingUser) {
                // Авторизуем существующего пользователя
                \Auth::login($existingUser);
                
                return response()->json([
                    'success' => true,
                    'redirect' => route('home')
                ]);
            }
            
            // Генерируем уникальный username
            $username = $this->generateUsername(
                $validated['telegram_username'] ?? $validated['first_name'] ?? 'user'
            );
            
            // Получаем аватар из Telegram
            $avatar = $this->getTelegramUserAvatar($validated['telegram_id']);
            
            // Используем RegistrationService для создания пользователя
            $registered = $this->registrationService->register([
                'username' => $username,
                'telegram_id' => $validated['telegram_id'],
                'avatar' => $avatar,
                'currency' => $validated['currency'],
            ]);
            
            if ($registered) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('home')
                ]);
            }
            
            throw new \Exception('Failed to register user');
            
        } catch (\Exception $e) {
            \Log::error('Telegram code auth completion error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => __('Произошла ошибка при регистрации. Попробуйте снова.')
            ], 500);
        }
    }
    
    /**
     * Получить аватар пользователя из Telegram
     */
    private function getTelegramUserAvatar($telegramId)
    {
        try {
            Log::info('Getting Telegram avatar', ['telegram_id' => $telegramId]);
            
            // Получаем фото профиля пользователя
            $response = Http::get("https://api.telegram.org/bot{$this->botToken}/getUserProfilePhotos", [
                'user_id' => $telegramId,
                'limit' => 1
            ]);
            
            if (!$response->successful()) {
                Log::warning('Failed to get user profile photos', ['status' => $response->status()]);
                return '/assets/images/avatar-placeholder.png';
            }
            
            $data = $response->json();
            Log::info('Profile photos response', ['data' => $data]);
            
            // Проверяем есть ли фото
            if (!isset($data['result']['photos'][0][0]['file_id'])) {
                Log::info('No profile photo found for user');
                return '/assets/images/avatar-placeholder.png';
            }
            
            $fileId = $data['result']['photos'][0][0]['file_id'];
            
            // Получаем путь к файлу
            $fileResponse = Http::get("https://api.telegram.org/bot{$this->botToken}/getFile", [
                'file_id' => $fileId
            ]);
            
            if (!$fileResponse->successful()) {
                return '/assets/images/avatar-placeholder.png';
            }
            
            $fileData = $fileResponse->json();
            
            if (!isset($fileData['result']['file_path'])) {
                return '/assets/images/avatar-placeholder.png';
            }
            
            $filePath = $fileData['result']['file_path'];
            
            // Формируем URL для скачивания
            $fileUrl = "https://api.telegram.org/file/bot{$this->botToken}/{$filePath}";
            
            // Скачиваем и сохраняем аватар
            $avatarContent = Http::get($fileUrl)->body();
            
            // Создаём уникальное имя файла
            $fileName = 'telegram_' . $telegramId . '_' . time() . '.jpg';
            $savePath = public_path('images/avatars/' . $fileName);
            
            // Создаём директорию если её нет
            if (!file_exists(public_path('images/avatars'))) {
                mkdir(public_path('images/avatars'), 0755, true);
            }
            
            // Сохраняем файл
            file_put_contents($savePath, $avatarContent);
            
            Log::info('Avatar saved successfully', ['path' => '/images/avatars/' . $fileName]);
            
            return '/images/avatars/' . $fileName;
            
        } catch (\Exception $e) {
            Log::error('Error getting Telegram avatar: ' . $e->getMessage(), [
                'telegram_id' => $telegramId,
                'trace' => $e->getTraceAsString()
            ]);
            return '/assets/images/avatar-placeholder.png';
        }
    }
    
    /**
     * Генерация уникального username
     */
    private function generateUsername($base)
    {
        $base = preg_replace('/[^a-zA-Z0-9_]/', '', $base);
        $base = strtolower($base);
        
        if (strlen($base) < 3) {
            $base = 'user' . $base;
        }
        
        $username = $base;
        $counter = 1;
        
        while (\App\Models\User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }
        
        return $username;
    }
}
