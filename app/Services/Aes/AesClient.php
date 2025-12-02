<?php

namespace App\Services\Aes;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class AesClient
{
    protected string $baseUrl;
    protected string $apiToken;
    protected Client $client;

    public function __construct(?string $currency = null)
    {
        $this->baseUrl = config('services.aes.base_url');
        $this->apiToken = $this->getApiTokenForCurrency($currency);

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'http_errors' => false,
        ]);
    }

    protected function getApiTokenForCurrency(?string $currency): string
    {
        $accounts = config('services.aes.accounts', []);

        // Если валюта указана и есть аккаунт для неё
        if ($currency && isset($accounts[$currency])) {
            return $accounts[$currency]['api_token'];
        }

        // Fallback: берём первый доступный токен
        foreach ($accounts as $account) {
            if (!empty($account['api_token'])) {
                return $account['api_token'];
            }
        }

        // Последний fallback на старый формат
        return config('services.aes.api_token', '');
    }

    public function getAgentInfo(): array
    {
        return $this->post('/v4/agent/info', []);
    }

    public function createUser(string $username): array
    {
        return $this->post('/v4/user/create', ['name' => $username]);
    }

    public function getUserInfo(int $userCode): array
    {
        return $this->post('/v4/user/info', ['user_code' => $userCode]);
    }

    public function depositBalance(int $userCode, float $amount): array
    {
        return $this->post('/v4/wallet/deposit', [
            'user_code' => $userCode,
            'amount' => $amount
        ]);
    }

    public function withdrawBalance(int $userCode, float $amount): array
    {
        return $this->post('/v4/wallet/withdraw', [
            'user_code' => $userCode,
            'amount' => $amount
        ]);
    }

    public function withdrawAllBalance(int $userCode): array
    {
        return $this->post('/v4/wallet/withdraw-all', ['user_code' => $userCode]);
    }

    public function getProviders(int $lang = 1): array
    {
        return $this->post('/v4/game/providers', ['lang' => $lang]);
    }

    public function getGames(int $providerId, int $lang = 1): array
    {
        return $this->post('/v4/game/games', [
            'provider_id' => $providerId,
            'lang' => $lang
        ]);
    }

    public function getGameUrl(array $params): array
    {
        $data = [
            'user_code' => $params['user_code'],
            'provider_id' => $params['provider_id'],
            'game_symbol' => $params['game_symbol'],
            'lang' => $params['lang'] ?? 1,
            'return_url' => $params['return_url'] ?? config('app.url'),
            'win_ratio' => $params['win_ratio'] ?? 0,
        ];

        return $this->post('/v4/game/game-url', $data);
    }

    public function getOnlineGames(): array
    {
        return $this->post('/v4/game/online-games', []);
    }

    public function getTransactions(string $startTime, string $endTime, int $offset = 0, int $limit = 100): array
    {
        return $this->post('/v4/game/transaction', [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'offset' => $offset,
            'limit' => min($limit, 2000)
        ]);
    }

    public function getTransactionsById(int $lastId = 0, int $limit = 100): array
    {
        return $this->post('/v4/game/transaction-id', [
            'last_id' => $lastId,
            'limit' => min($limit, 2000)
        ]);
    }

    public function getRoundDetails(int $userCode, string $roundId, ?int $providerId = null, ?string $gameCode = null): array
    {
        $data = [
            'user_code' => $userCode,
            'round_id' => $roundId,
        ];

        if ($providerId) {
            $data['provider_id'] = $providerId;
        }

        if ($gameCode) {
            $data['game_code'] = $gameCode;
        }

        return $this->post('/v4/game/round-details', $data);
    }

    protected function post(string $endpoint, array $data): array
    {
        try {
            $response = $this->client->post($endpoint, [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);

            if ($statusCode !== 200) {
                Log::error('AES API HTTP Error', [
                    'endpoint' => $endpoint,
                    'status_code' => $statusCode
                ]);
                return [
                    'code' => 1001,
                    'message' => "HTTP Error: {$statusCode}",
                    'data' => null
                ];
            }

            if (!$result) {
                return [
                    'code' => 1001,
                    'message' => 'Invalid JSON response',
                    'data' => null
                ];
            }

            if (isset($result['code']) && $result['code'] !== 0) {
                Log::error('AES API Error', [
                    'endpoint' => $endpoint,
                    'code' => $result['code'],
                    'message' => $result['message'] ?? 'Unknown error'
                ]);
            }

            return $result;

        } catch (GuzzleException $e) {
            Log::error('AES API Connection Error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            return [
                'code' => 1001,
                'message' => 'Connection error: ' . $e->getMessage(),
                'data' => null
            ];
        } catch (\Exception $e) {
            Log::error('AES API Unexpected Error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            return [
                'code' => 1001,
                'message' => 'Unexpected error: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function mapLanguage(string $locale): int
    {
        $languageMap = [
            'en' => 1,
            'ko' => 2,
            'th' => 3,
            'es' => 4,
            'ja' => 5,
            'pt' => 6,
            'tr' => 7,
            'de' => 8,
            'az' => 1,
            'ru' => 1,
            'uz' => 1
        ];

        return $languageMap[$locale] ?? 1;
    }

    public function isSuccess(array $response): bool
    {
        return isset($response['code']) && $response['code'] === 0;
    }

    public function getErrorMessage(array $response): string
    {
        return $response['message'] ?? 'Unknown error';
    }
}
