<?php

namespace App\Services;

use App\Models\UserCryptoWallet;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WestWalletService
{
    private string $apiUrl;
    private string $publicKey;
    private string $privateKey;

    public function __construct()
    {
        $this->apiUrl = config('payment.westwallet.api_url');
        $this->publicKey = config('payment.westwallet.public_key');
        $this->privateKey = config('payment.westwallet.private_key');
    }

    /**
     * Генерация HMAC подписи для запроса
     * Формат согласно документации: HMAC-SHA256(timestamp + json_dumps(data), private_key)
     * ensure_ascii=False в Python = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES в PHP
     */
    private function generateSignature(int $timestamp, array $data = []): string
    {
        if (!empty($data)) {
            $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            $jsonData = '';
        }
        
        $message = $timestamp . $jsonData;
        
        Log::debug('WestWallet HMAC Debug', [
            'timestamp' => $timestamp,
            'data' => $data,
            'jsonData' => $jsonData,
            'message' => $message,
            'signature' => hash_hmac('sha256', $message, $this->privateKey)
        ]);
        
        return hash_hmac('sha256', $message, $this->privateKey);
    }

    /**
     * Выполнение API запроса к WestWallet
     */
    private function makeRequest(string $endpoint, string $method = 'GET', array $data = []): array
    {
        try {
            $timestamp = time();
            // Для GET и POST запросов всегда передаем data в generateSignature
            $signature = $this->generateSignature($timestamp, $data);

            $headers = [
                'Content-Type' => 'application/json',
                'X-API-KEY' => $this->publicKey,
                'X-ACCESS-SIGN' => $signature,
                'X-ACCESS-TIMESTAMP' => (string)$timestamp,
            ];

            $url = $this->apiUrl . $endpoint;

            $response = $method === 'GET'
                ? Http::withHeaders($headers)->get($url, $data)
                : Http::withHeaders($headers)->post($url, $data);

            if (!$response->successful()) {
                Log::error('WestWallet API Error', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'error' => true,
                    'message' => 'Ошибка при обращении к WestWallet API',
                    'status' => $response->status()
                ];
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('WestWallet API Exception', [
                'endpoint' => $endpoint,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Получить или создать крипто-кошелек для пользователя
     */
    public function getOrCreateWallet(User $user, string $currency, ?string $network = null): array
    {
        // Проверяем, есть ли уже кошелек
        $existingWallet = UserCryptoWallet::where('user_id', $user->id)
            ->where('currency', strtoupper($currency))
            ->when($network, fn($q) => $q->where('network', $network))
            ->first();

        if ($existingWallet) {
            return [
                'error' => false,
                'data' => [
                    'address' => $existingWallet->address,
                    'dest_tag' => $existingWallet->dest_tag,
                    'currency' => $existingWallet->currency,
                    'network' => $existingWallet->network,
                    'label' => $existingWallet->label,
                    'existing' => true
                ]
            ];
        }

        // Генерируем новый адрес через WestWallet API
        return $this->generateAddress($user, $currency, $network);
    }

    /**
     * Генерация нового крипто-адреса
     */
    public function generateAddress(User $user, string $currency, ?string $network = null): array
    {
        $label = UserCryptoWallet::generateLabel($user->id, $currency, $network);
        $ipnUrl = route('westwallet.callback');

        $data = [
            'currency' => strtoupper($currency),
            'ipn_url' => $ipnUrl,
            'label' => $label,
        ];

        $response = $this->makeRequest('/address/generate', 'POST', $data);

        if (!isset($response['error']) || $response['error'] !== 'ok') {
            Log::error('WestWallet Address Generation Failed', [
                'user_id' => $user->id,
                'currency' => $currency,
                'response' => $response
            ]);

            $errorMsg = 'Не удалось сгенерировать адрес';
            if (isset($response['error'])) {
                $errorMsg .= ': ' . $response['error'];
            }

            return [
                'error' => true,
                'message' => $errorMsg
            ];
        }

        // WestWallet возвращает данные напрямую в ответе, а не в поле 'data'
        if (!isset($response['address'])) {
            Log::error('WestWallet Address Missing', [
                'user_id' => $user->id,
                'currency' => $currency,
                'response' => $response
            ]);

            return [
                'error' => true,
                'message' => 'Адрес не найден в ответе API'
            ];
        }

        // Сохраняем адрес в БД
        try {
            $wallet = UserCryptoWallet::create([
                'user_id' => $user->id,
                'currency' => strtoupper($currency),
                'address' => $response['address'],
                'dest_tag' => $response['dest_tag'] ?? null,
                'label' => $label,
                'network' => $network,
            ]);

            Log::info('Crypto wallet created', [
                'user_id' => $user->id,
                'currency' => $currency,
                'address' => $wallet->address,
                'label' => $label
            ]);

            return [
                'error' => false,
                'data' => [
                    'address' => $wallet->address,
                    'dest_tag' => $wallet->dest_tag,
                    'currency' => $wallet->currency,
                    'network' => $wallet->network,
                    'label' => $wallet->label,
                    'existing' => false
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Failed to save crypto wallet', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'error' => true,
                'message' => 'Ошибка при сохранении адреса'
            ];
        }
    }

    /**
     * Получить информацию о валютах
     */
    public function getCurrenciesData(): array
    {
        // Кэшируем на 1 час
        return Cache::remember('westwallet_currencies', 3600, function () {
            return $this->makeRequest('/wallet/currencies_data', 'GET');
        });
    }

    /**
     * Проверить статус транзакции по ID
     */
    public function getTransactionStatus(int $transactionId): array
    {
        return $this->makeRequest('/wallet/transaction', 'POST', [
            'id' => $transactionId
        ]);
    }

    /**
     * Проверить статус транзакции по label
     */
    public function getTransactionByLabel(string $label): array
    {
        return $this->makeRequest('/wallet/transaction', 'POST', [
            'label' => $label
        ]);
    }

    /**
     * Получить баланс кошелька
     */
    public function getBalance(string $currency): array
    {
        return $this->makeRequest('/wallet/balance', 'GET', [
            'currency' => strtoupper($currency)
        ]);
    }

    /**
     * Получить историю транзакций
     */
    public function getTransactions(array $params = []): array
    {
        $defaultParams = [
            'limit' => 10,
            'offset' => 0,
            'order' => 'desc',
        ];

        $params = array_merge($defaultParams, $params);

        return $this->makeRequest('/wallet/transactions', 'POST', $params);
    }

    /**
     * Проверка доступности валюты для депозита
     */
    public function isCurrencyAvailable(string $currency): bool
    {
        $currencies = $this->getCurrenciesData();
        
        if (isset($currencies['error']) && $currencies['error'] !== 'ok') {
            return false;
        }

        $currencyData = collect($currencies['data'] ?? [])
            ->firstWhere('tickers', fn($tickers) => in_array(strtoupper($currency), $tickers));

        return $currencyData && ($currencyData['active'] ?? false) && ($currencyData['receive_active'] ?? false);
    }

    /**
     * Получить минимальную сумму депозита для валюты
     */
    public function getMinDeposit(string $currency): ?float
    {
        $currencies = $this->getCurrenciesData();
        
        if (isset($currencies['error']) && $currencies['error'] !== 'ok') {
            return null;
        }

        $currencyData = collect($currencies['data'] ?? [])
            ->firstWhere('tickers', fn($tickers) => in_array(strtoupper($currency), $tickers));

        return $currencyData['min_receive'] ?? null;
    }
}
