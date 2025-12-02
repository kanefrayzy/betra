<?php

namespace App\Services\Tbs2;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class Tbs2Client
{
    protected string $baseUrl;
    protected string $hallId;
    protected string $hallKey;

    public function __construct()
    {
        $this->baseUrl = config('services.tbs2.base_url');
        $this->hallId = config('services.tbs2.hall_id');
        $this->hallKey = config('services.tbs2.hall_key');
    }

    /**
     * Получение списка игр
     */
    public function getGamesList(): array
    {
        try {
            $data = [
                'hall' => $this->hallId,
                'key' => $this->hallKey,
                'cmd' => 'gamesList',
                'cdnUrl' => '/'
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl); // Убираем лишний слэш
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Отправляем JSON
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data))
            ]);

            $result = curl_exec($ch);

            if ($result === false) {
                throw new Exception('Curl error: ' . curl_error($ch));
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                throw new Exception("HTTP error: {$httpCode}");
            }

            $decodedResult = json_decode($result, true);

            if (!$decodedResult) {
                throw new Exception('Invalid JSON response from TBS2');
            }

            Log::info('TBS2 games list received', [
                'status' => $decodedResult['status'] ?? 'unknown',
                'games_count' => count($decodedResult['content']['games'] ?? []),
                'providers' => $decodedResult['content']['gameTitles'] ?? [],
                'has_provider_data' => isset($decodedResult['content']['providerData']),
                'response_keys' => array_keys($decodedResult['content'] ?? []),
                'full_response' => $decodedResult // Для отладки
            ]);

            return $decodedResult;

        } catch (Exception $e) {
            Log::error('TBS2 games list error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception("Failed to get games list: " . $e->getMessage());
        }
    }

    public function openGame(array $params): array
    {
        $locale = App::getLocale();
        $locale = $locale === 'az' ? 'tr' : $locale;
        try {
            $data = [
                'cmd' => 'openGame',
                'hall' => $this->hallId,
                'key' => $this->hallKey,
                'login' => $params['login'],
                'gameId' => $params['gameId'],
                'language' => $locale,
                'demo' => $params['demo'] ?? '0',
                'domain' => config('app.url'),
                'exitUrl' => config('app.url') . '/games/close',
                'continent' => 'eur',
                'device' => '1',
                'sessionId' => $params['sessionId'] ?? Str::uuid()->toString(),
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl . 'openGame/');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec($ch);

            if ($result === false) {
                throw new Exception('Curl error: ' . curl_error($ch));
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            Log::info('TBS2 API Response', [
                'http_code' => $httpCode,
                'response' => $result,
                'data_sent' => $data
            ]);

            $result = json_decode($result, true);

            if (!$result) {
                throw new Exception('Invalid JSON response from TBS2');
            }

            return $result;

        } catch (Exception $e) {
            Log::error('TBS2 open game error', [
                'error' => $e->getMessage(),
                'params' => $params
            ]);
            throw new Exception("Failed to open game: " . $e->getMessage());
        }
    }

    /**
     * Обработка getBalance
     */
    public function handleGetBalance(array $data): array
    {
        return [
            'status' => 'success',
            'error' => '',
            'login' => $data['login'],
            'balance' => $data['balance'],
            'currency' => $data['currency'] ?? 'RUB'
        ];
    }

    /**
     * Обработка writeBet
     */
    public function handleWriteBet(array $data): array
    {
        return [
            'status' => 'success',
            'error' => '',
            'login' => $data['login'],
            'balance' => $data['balance'],
            'currency' => $data['currency'] ?? 'USD'
        ];
    }

    /**
     * Получение джекпотов
     */
    public function getJackpots(): array
    {
        try {
            $data = [
                'hall' => $this->hallId,
                'key' => $this->hallKey,
                'cmd' => 'jackpots'
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec($ch);

            if ($result === false) {
                throw new Exception('Curl error: ' . curl_error($ch));
            }

            curl_close($ch);

            return json_decode($result, true);

        } catch (Exception $e) {
            Log::error('TBS2 jackpots error', [
                'error' => $e->getMessage()
            ]);
            return ['status' => 'error', 'content' => []];
        }
    }

    /**
     * Получение логов игровой сессии
     */
    public function getGameSessionsLog(string $sessionId, int $page = 1, int $count = 50): array
    {
        try {
            $data = [
                'cmd' => 'gameSessionsLog',
                'hall' => $this->hallId,
                'key' => $this->hallKey,
                'sessionsId' => $sessionId,
                'count' => $count,
                'page' => $page
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec($ch);

            if ($result === false) {
                throw new Exception('Curl error: ' . curl_error($ch));
            }

            curl_close($ch);

            return json_decode($result, true);

        } catch (Exception $e) {
            Log::error('TBS2 session log error', [
                'error' => $e->getMessage(),
                'sessionId' => $sessionId
            ]);
            return ['status' => 'error', 'content' => []];
        }
    }

    /**
     * Возврат ошибки для getBalance
     */
    public function balanceErrorResponse(string $errorCode): array
    {
        return [
            'status' => 'fail',
            'error' => $errorCode
        ];
    }

    /**
     * Возврат ошибки для writeBet
     */
    public function betErrorResponse(string $errorCode): array
    {
        return [
            'status' => 'fail',
            'error' => $errorCode
        ];
    }
}
