<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected $botToken;
    protected $channelId;

    public function __construct()
    {
        $this->botToken = config('telegram.bot_token');
        $this->channelId = config('telegram.channel_id');
    }

    public function sendDailyBonusMessage($link)
    {
        $message = $this->formatDailyBonusMessage();
        $photoUrl = asset('assets/images/dailybonus.png');

        $url = "https://api.telegram.org/bot{$this->botToken}/sendPhoto";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🎁 Получить бонус!', 'url' => $link]
                ]
            ]
        ];

        $response = Http::post($url, [
            'chat_id' => $this->channelId,
            'photo' => $photoUrl,
            'caption' => $message,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode($keyboard)
        ]);

        if (!$response->successful()) {
            \Log::error('Failed to send Telegram message: ' . $response->body());
        }
    }

    private function formatDailyBonusMessage()
    {
        return "<b>⚡️ Ежедневный бонус доступен!</b>\n\n" .
               "Система лояльности FlashGame награждает всех активных игроков каждый день отличными призами.\n\n" .
               "Размер бонуса зависит от вашего текущего уровня. 🎉";
    }

    public function isUserMember($userId)
    {
        try {
            $response = Http::timeout(5)->get("https://api.telegram.org/bot{$this->botToken}/getChatMember", [
                'chat_id' => $this->channelId,
                'user_id' => $userId
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return in_array($result['result']['status'], ['creator', 'administrator', 'member']);
            }

            return true;

        } catch (\Exception $e) {
            return true;
        }
    }
}
