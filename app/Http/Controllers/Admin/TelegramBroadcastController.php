<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TelegramMessageTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TelegramBroadcastController extends Controller
{
    /**
     * Показать страницу рассылки
     */
    public function index()
    {
        $u = auth()->user();
        $settings = \App\Models\Settings::first();

        // Статистика пользователей с Telegram
        $totalUsers = User::whereNotNull('telegram_id')->count();
        $activeUsers = User::whereNotNull('telegram_id')
            ->where('last_login_at', '>=', now()->subDays(7))
            ->count();
        $inactiveUsers = User::whereNotNull('telegram_id')
            ->where('last_login_at', '<', now()->subDays(7))
            ->count();

        // Получить шаблоны
        $templates = TelegramMessageTemplate::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        // История рассылок из кэша
        $broadcastHistory = Cache::get('telegram_broadcast_history', []);
        $broadcastHistory = array_slice(array_reverse($broadcastHistory), 0, 10);

        return view('admin.telegram-broadcast', compact(
            'u',
            'settings',
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'templates',
            'broadcastHistory'
        ));
    }

    /**
     * Предпросмотр сообщения
     */
    public function preview(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:4096'
        ]);

        $message = $this->formatMessage($request->message);

        return response()->json([
            'success' => true,
            'preview' => nl2br(e($message))
        ]);
    }

    /**
     * Отправить рассылку
     */
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:4096',
            'target' => 'required|in:all,active,inactive,specific,single',
            'user_ids' => 'nullable|string',
            'single_user_id' => 'nullable|integer',
            'template_id' => 'nullable|integer|exists:telegram_message_templates,id',
            'has_buttons' => 'nullable|boolean',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|url|max:500'
        ]);

        $botToken = config('telegram.bot_token');
        if (!$botToken) {
            return response()->json([
                'success' => false,
                'message' => 'Токен Telegram бота не настроен'
            ], 500);
        }

        // Получаем целевых пользователей
        $users = $this->getTargetUsers($request->target, $request->user_ids, $request->single_user_id);

        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Не найдено пользователей для рассылки'
            ], 400);
        }

        // Обновляем счетчик использования шаблона
        if ($request->template_id) {
            $template = TelegramMessageTemplate::find($request->template_id);
            if ($template) {
                $template->incrementUsage();
            }
        }

        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        // Подготовка клавиатуры если есть кнопки
        $replyMarkup = null;
        if ($request->has_buttons && $request->button_text && $request->button_url) {
            $replyMarkup = [
                'inline_keyboard' => [
                    [
                        [
                            'text' => $request->button_text,
                            'web_app' => ['url' => $request->button_url]
                        ]
                    ]
                ]
            ];
        }

        foreach ($users as $user) {
            try {
                $message = $this->formatMessage($request->message, $user);
                
                $params = [
                    'chat_id' => $user->telegram_id,
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ];

                if ($replyMarkup) {
                    $params['reply_markup'] = json_encode($replyMarkup);
                }

                $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", $params);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $failedCount++;
                    $errors[] = "User {$user->id}: " . $response->json('description', 'Unknown error');
                }

                // Небольшая задержка чтобы не превысить лимит API Telegram
                usleep(50000); // 50ms между сообщениями
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = "User {$user->id}: " . $e->getMessage();
                Log::error('Telegram broadcast error', [
                    'user_id' => $user->id,
                    'telegram_id' => $user->telegram_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Сохраняем в историю
        $this->saveBroadcastHistory([
            'date' => now()->format('Y-m-d H:i:s'),
            'target' => $request->target,
            'total_users' => $users->count(),
            'success' => $successCount,
            'failed' => $failedCount,
            'message_preview' => mb_substr($request->message, 0, 100) . (mb_strlen($request->message) > 100 ? '...' : ''),
            'sent_by' => auth()->user()->username,
            'has_buttons' => $request->has_buttons ?? false
        ]);

        return response()->json([
            'success' => true,
            'message' => "Рассылка завершена! Отправлено: {$successCount}, Ошибок: {$failedCount}",
            'stats' => [
                'total' => $users->count(),
                'success' => $successCount,
                'failed' => $failedCount
            ],
            'errors' => array_slice($errors, 0, 10) // Первые 10 ошибок
        ]);
    }

    /**
     * Получить целевых пользователей для рассылки
     */
    private function getTargetUsers($target, $userIds = null, $singleUserId = null)
    {
        $query = User::whereNotNull('telegram_id');

        switch ($target) {
            case 'active':
                $query->where('last_login_at', '>=', now()->subDays(7));
                break;
            case 'inactive':
                $query->where('last_login_at', '<', now()->subDays(7));
                break;
            case 'specific':
                if ($userIds) {
                    $ids = array_map('trim', explode(',', $userIds));
                    $query->whereIn('id', $ids);
                }
                break;
            case 'single':
                if ($singleUserId) {
                    $query->where('id', $singleUserId);
                }
                break;
            case 'all':
            default:
                // Все пользователи с telegram_id
                break;
        }

        return $query->select('id', 'telegram_id', 'username')->get();
    }

    /**
     * Форматировать сообщение с заменой переменных
     */
    private function formatMessage($message, $user = null)
    {
        $settings = \App\Models\Settings::first();
        
        $replacements = [
            '{sitename}' => $settings->sitename ?? 'FlashGame',
            '{date}' => now()->format('d.m.Y'),
            '{time}' => now()->format('H:i'),
            '{username}' => $user ? $user->username : ''
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }

    /**
     * Сохранить историю рассылки
     */
    private function saveBroadcastHistory($data)
    {
        $history = Cache::get('telegram_broadcast_history', []);
        $history[] = $data;

        // Храним последние 100 рассылок
        if (count($history) > 100) {
            $history = array_slice($history, -100);
        }

        Cache::put('telegram_broadcast_history', $history, now()->addDays(30));
    }

    /**
     * Получить статистику по пользователям
     */
    public function getUserStats()
    {
        $stats = [
            'total' => User::whereNotNull('telegram_id')->count(),
            'active_today' => User::whereNotNull('telegram_id')
                ->where('last_login_at', '>=', now()->subDay())
                ->count(),
            'active_week' => User::whereNotNull('telegram_id')
                ->where('last_login_at', '>=', now()->subDays(7))
                ->count(),
            'active_month' => User::whereNotNull('telegram_id')
                ->where('last_login_at', '>=', now()->subDays(30))
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Поиск пользователя по ID или username
     */
    public function searchUser(Request $request)
    {
        $query = $request->get('query');
        
        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Введите ID или username пользователя'
            ]);
        }

        $user = User::whereNotNull('telegram_id')
            ->where(function($q) use ($query) {
                $q->where('id', $query)
                  ->orWhere('username', 'like', "%{$query}%");
            })
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не найден или не имеет Telegram'
            ]);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'telegram_id' => $user->telegram_id,
                'avatar' => $user->avatar,
                'last_login' => $user->last_login_at ? $user->last_login_at->format('d.m.Y H:i') : 'Никогда'
            ]
        ]);
    }

    /**
     * Получить шаблон по ID
     */
    public function getTemplate($id)
    {
        $template = TelegramMessageTemplate::find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Шаблон не найден'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'template' => $template
        ]);
    }

    /**
     * Сохранить новый шаблон
     */
    public function saveTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string|max:4096',
            'category' => 'nullable|string|max:255',
            'has_buttons' => 'nullable|boolean',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|url|max:500'
        ]);

        $buttons = null;
        if ($request->has_buttons && $request->button_text && $request->button_url) {
            $buttons = [
                [
                    'text' => $request->button_text,
                    'url' => $request->button_url
                ]
            ];
        }

        $template = TelegramMessageTemplate::create([
            'name' => $request->name,
            'message' => $request->message,
            'category' => $request->category,
            'has_buttons' => $request->has_buttons ?? false,
            'buttons' => $buttons,
            'is_active' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Шаблон успешно создан',
            'template' => $template
        ]);
    }

    /**
     * Удалить шаблон
     */
    public function deleteTemplate($id)
    {
        $template = TelegramMessageTemplate::find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Шаблон не найден'
            ], 404);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Шаблон успешно удален'
        ]);
    }
}
