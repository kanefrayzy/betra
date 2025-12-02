<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WebSocketService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function sendData(array $data)
    {
        try {
            $response = $this->client->post('https://flashgame.live:3000/update-transactions', [
                'json' => [
                    'type' => 'updateTable',
                    'data' => $data
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                Log::info('Данные успешно отправлены на WebSocket сервер');
            } else {
                Log::error('Ошибка при отправке данных на WebSocket сервер: ' . $response->getStatusCode());
            }
        } catch (\Exception $e) {
            Log::error('Ошибка при отправке данных на WebSocket сервер: ' . $e->getMessage());
        }
    }
}
