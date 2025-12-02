<?php

namespace App\Services\B2bSlots;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class B2bSlotsClient
{
    protected string $baseUrl;
    protected string $operatorId;
    protected Client $client;

    public function __construct()
    {
        $this->baseUrl = config('services.b2b_slots.base_url', 'https://int.apichannel.cloud');
        $this->operatorId = config('services.b2b_slots.operator_id');
        $this->client = new Client();
    }

    /**
     * Генерация URL для запуска игры
     */
    public function generateGameUrl(array $params): string
    {
        $query = http_build_query([
            'operator_id' => $this->operatorId,
            'user_id' => $params['user_id'],
            'auth_token' => $params['auth_token'],
            'currency' => $params['currency'],
            'language' => $params['language'] ?? 'en',
            'home_url' => $params['home_url'] ?? route('home'),
        ]);

        if (isset($params['game_code'])) {
            return "{$this->baseUrl}/gamesbycode/{$params['game_code']}.gamecode?{$query}";
        }

        return "{$this->baseUrl}/games/{$params['game_name']}.game?{$query}";
    }

    /**
     * Обработка Auth API
     */
    public function handleAuth(array $data): array
    {
        // Проверяем токен и возвращаем данные пользователя
        return [
            'api' => 'do-auth-user-ingame',
            'success' => true,
            'answer' => [
                'operator_id' => (int)$this->operatorId,
                'user_id' => $data['user_id'],
                'user_nickname' => $data['user_nickname'],
                'balance' => $data['balance'],
                'bonus_balance' => $data['bonus_balance'] ?? '0.00',
                'auth_token' => $data['user_auth_token'],
                'game_token' => $data['game_token'],
                'error_code' => 0,
                'error_description' => 'ok',
                'currency' => $data['currency'],
                'timestamp' => (string)(Carbon::now()->timestamp * 1000),
            ]
        ];
    }

    /**
     * Обработка Debit API (ставка)
     */
    public function handleDebit(array $data): array
    {
        return [
            'api' => 'do-debit-user-ingame',
            'success' => true,
            'answer' => [
                'operator_id' => (int)$this->operatorId,
                'transaction_id' => $data['transaction_id'],
                'user_id' => $data['user_id'],
                'user_nickname' => $data['user_nickname'],
                'balance' => $data['balance'],
                'bonus_balance' => $data['bonus_balance'] ?? '0.00',
                'bonus_amount' => $data['bonus_amount'] ?? '0.00',
                'game_token' => $data['game_token'],
                'error_code' => 0,
                'error_description' => 'ok',
                'currency' => $data['currency'],
                'timestamp' => (string)(Carbon::now()->timestamp * 1000),
            ]
        ];
    }

    /**
     * Обработка Credit API (выигрыш)
     */
    public function handleCredit(array $data): array
    {
        return [
            'api' => 'do-credit-user-ingame',
            'success' => true,
            'answer' => [
                'operator_id' => (int)$this->operatorId,
                'transaction_id' => $data['transaction_id'],
                'user_id' => $data['user_id'],
                'user_nickname' => $data['user_nickname'],
                'balance' => $data['balance'],
                'bonus_balance' => $data['bonus_balance'] ?? '0.00',
                'bonus_amount' => $data['bonus_amount'] ?? '0.00',
                'game_token' => $data['game_token'],
                'error_code' => 0,
                'error_description' => 'ok',
                'currency' => $data['currency'],
                'timestamp' => (string)(Carbon::now()->timestamp * 1000),
            ]
        ];
    }

    /**
     * Обработка Get Features API (фриспины)
     */
    public function handleGetFeatures(array $data): array
    {
        return [
            'api' => 'do-get-features-user-ingame',
            'success' => true,
            'answer' => [
                'operator_id' => (int)$this->operatorId,
                'user_id' => $data['user_id'],
                'user_nickname' => $data['user_nickname'],
                'balance' => $data['balance'],
                'bonus_balance' => $data['bonus_balance'] ?? '0.00',
                'game_token' => $data['game_token'],
                'error_code' => 0,
                'error_description' => 'ok',
                'currency' => $data['currency'],
                'timestamp' => (string)(Carbon::now()->timestamp * 1000),
                'free_rounds' => $data['free_rounds'] ?? null,
            ]
        ];
    }

    /**
     * Получение списка игр
     */
    public function getGamesList(): array
    {
        try {
            $url = "{$this->baseUrl}/frontendsrv/apihandler.api";
            $query = [
                'cmd' => json_encode([
                    'api' => 'ls-games-by-operator-idget',
                    'operator_id' => $this->operatorId
                ])
            ];

            $response = $this->client->get($url, ['query' => $query]);
            return json_decode($response->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            Log::error('B2B Slots games list error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception("Failed to get games list");
        }
    }

    /**
     * Возврат ошибки
     */
    public function errorResponse(string $api, int $errorCode, string $description): array
    {
        return [
            'api' => $api,
            'success' => true,
            'answer' => [
                'operator_id' => (int)$this->operatorId,
                'error_code' => $errorCode,
                'error_description' => $description,
                'timestamp' => (string)(Carbon::now()->timestamp * 1000),
            ]
        ];
    }
}
