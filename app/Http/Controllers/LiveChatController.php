<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LiveChatController extends Controller
{
    private $liveChatApi;

    public function __construct()
    {
        // Инициализация API LiveChat
        $this->liveChatApi = new \LiveChat\Api([
            'client_id' => env('LIVECHAT_CLIENT_ID'),
            'client_secret' => env('LIVECHAT_CLIENT_SECRET')
        ]);
    }

    // Веб-хук для получения событий LiveChat
    public function handleWebhook(Request $request)
    {
        $event = $request->all();

        if ($event['event_type'] === 'chat_started') {
            // Получаем информацию о посетителе
            $visitorEmail = $event['visitor']['email'];

            // Ищем пользователя в БД
            $user = User::where('email', $visitorEmail)->first();

            if ($user) {
                // Отправляем информацию о пользователе в LiveChat
                $this->liveChatApi->updateVisitor($event['visitor']['id'], [
                    'custom_variables' => [
                        [
                            'name' => 'user_id',
                            'value' => $user->id
                        ],
                        [
                            'name' => 'registered_at',
                            'value' => $user->created_at->format('Y-m-d')
                        ],
                        // Добавьте другие нужные поля
                    ]
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
