<?php

namespace App\Services\Betvio;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class BetvioClient
{
    protected string $baseUrl;
    protected string $agentCode;
    protected string $agentToken;
    protected string $agentSecret;
    protected string $currency;
    protected Client $client;

    public function __construct(?string $currency = null)
    {
        $this->baseUrl = config('services.betvio.base_url');
        $this->loadAccountForCurrency($currency);

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'http_errors' => false,
        ]);
    }

    /**
     * Загружает учетные данные аккаунта для указанной валюты
     */
    protected function loadAccountForCurrency(?string $currency): void
    {
        $accounts = config('services.betvio.accounts', []);

        // Если валюта указана и есть аккаунт для неё
        if ($currency && isset($accounts[$currency])) {
            $account = $accounts[$currency];
        } else {
            // Fallback: берём первый доступный аккаунт
            $account = reset($accounts);
            if (!$account) {
                throw new \Exception('No Betvio accounts configured');
            }
        }

        $this->agentCode = $account['agent_code'] ?? '';
        $this->agentToken = $account['agent_token'] ?? '';
        $this->agentSecret = $account['agent_secret'] ?? '';
        $this->currency = $currency ?? array_search($account, $accounts) ?: 'USD';

        if (empty($this->agentCode) || empty($this->agentToken)) {
            throw new \Exception('Invalid Betvio account configuration');
        }
    }

    /**
     * Запуск игры
     */
    public function launchGame(array $params): array
    {
        $data = [
            'agent_code' => $this->agentCode,
            'agent_token' => $this->agentToken,
            'user_code' => $params['user_code'],
            'game_type' => $params['game_type'] ?? 'slot',
            'provider_code' => $params['provider_code'],
            'game_code' => $params['game_code'],
            'lang' => $params['lang'] ?? 'ru',
            'user_balance' => $params['user_balance'] ?? 0,
        ];

        return $this->post('/api/v2/game_launch', $data);
    }

    /**
     * Получить информацию об агенте и пользователях
     */
    public function getAgentInfo(): array
    {
        return $this->post('/api/v2/info', [
            'agent_code' => $this->agentCode,
            'agent_token' => $this->agentToken,
        ]);
    }

    /**
     * Получить список провайдеров
     */
    public function getProviders(string $gameType = 'slot'): array
    {
        return $this->post('/api/v2/provider_list', [
            'agent_code' => $this->agentCode,
            'agent_token' => $this->agentToken,
            'game_type' => $gameType,
        ]);
    }

    /**
     * Получить список игр провайдера
     */
    public function getGames(string $providerCode, string $lang = 'en'): array
    {
        return $this->post('/api/v2/game_list', [
            'agent_code' => $this->agentCode,
            'agent_token' => $this->agentToken,
            'provider_code' => $providerCode,
            'lang' => $lang,
        ]);
    }

    /**
     * Изменить RTP агента (0-95)
     */
    public function setAgentRtp(int $rtp): array
    {
        return $this->post('/api/v2/agent_rtp', [
            'agent_code' => $this->agentCode,
            'agent_token' => $this->agentToken,
            'agent_rtp' => $rtp,
        ]);
    }

    /**
     * Изменить RTP пользователя (0-95)
     */
    public function setUserRtp(string $userCode, string $providerCode, int $rtp): array
    {
        return $this->post('/api/v2/user_rtp', [
            'agent_code' => $this->agentCode,
            'agent_token' => $this->agentToken,
            'provider_code' => $providerCode,
            'user_code' => $userCode,
            'user_rtp' => $rtp,
        ]);
    }

    /**
     * Выполнить POST запрос
     */
    protected function post(string $endpoint, array $data): array
    {
        try {
            $response = $this->client->post($endpoint, [
                'json' => $data,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);

            if ($statusCode !== 200) {
                Log::error('Betvio API HTTP Error', [
                    'endpoint' => $endpoint,
                    'status_code' => $statusCode,
                    'body' => $body
                ]);
                return [
                    'status' => 0,
                    'msg' => "HTTP Error: {$statusCode}",
                ];
            }

            if (!$result) {
                return [
                    'status' => 0,
                    'msg' => 'Invalid JSON response',
                ];
            }

            // Логируем ошибки API
            if (isset($result['status']) && $result['status'] !== 1) {
                Log::error('Betvio API Error', [
                    'endpoint' => $endpoint,
                    'status' => $result['status'],
                    'message' => $result['msg'] ?? 'Unknown error'
                ]);
            }

            return $result;

        } catch (GuzzleException $e) {
            Log::error('Betvio API Connection Error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            return [
                'status' => 0,
                'msg' => 'Connection error: ' . $e->getMessage(),
            ];
        } catch (\Exception $e) {
            Log::error('Betvio API Unexpected Error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            return [
                'status' => 0,
                'msg' => 'Unexpected error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Маппинг языков
     */
    public function mapLanguage(string $locale): string
    {
        $languageMap = [
            'en' => 'en',
            'ko' => 'ko',
            'th' => 'th',
            'es' => 'es',
            'ja' => 'ja',
            'pt' => 'pt',
            'tr' => 'tr',
            'de' => 'de',
            'az' => 'en', // Betvio может не поддерживать азербайджанский
            'ru' => 'ru',
            'zh' => 'zh',
        ];

        return $languageMap[$locale] ?? 'en';
    }

    /**
     * Проверка успешности ответа
     */
    public function isSuccess(array $response): bool
    {
        return isset($response['status']) && $response['status'] === 1;
    }

    /**
     * Получить сообщение об ошибке
     */
    public function getErrorMessage(array $response): string
    {
        return $response['msg'] ?? 'Unknown error';
    }

    /**
     * Получить agent_code текущего аккаунта
     */
    public function getAgentCode(): string
    {
        return $this->agentCode;
    }

    /**
     * Получить agent_secret текущего аккаунта (для колбэков)
     */
    public function getAgentSecret(): string
    {
        return $this->agentSecret;
    }

    /**
     * Получить валюту текущего аккаунта
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Проверить agent_secret для колбэка
     */
    public function verifyCallbackSecret(string $receivedSecret): bool
    {
        return hash_equals($this->agentSecret, $receivedSecret);
    }

    /**
     * Получить все сконфигурированные валюты
     */
    public static function getConfiguredCurrencies(): array
    {
        return array_keys(config('services.betvio.accounts', []));
    }

    /**
     * Найти аккаунт по agent_code
     */
    public static function findAccountByAgentCode(string $agentCode): ?array
    {
        $accounts = config('services.betvio.accounts', []);
        
        foreach ($accounts as $currency => $account) {
            if ($account['agent_code'] === $agentCode) {
                return array_merge($account, ['currency' => $currency]);
            }
        }

        return null;
    }
}
