<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserCryptoWallet;
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
        $this->apiUrl = config('payment.westwallet.api_url', 'https://api.westwallet.io');
        $this->publicKey = config('payment.westwallet.public_key');
        $this->privateKey = config('payment.westwallet.private_key');
    }

    /**
     * Маппинг currency + network → WestWallet ticker
     * По аналогии с Node.js кодом
     */
    private function getCurrencyTicker(string $currency, ?string $network): string
    {
        // USDT с разными сетями
        if ($currency === 'USDT') {
            return match($network) {
                'TRC20' => 'USDTTRC',
                'ERC20' => 'USDT',
                'BEP20', 'BSC' => 'USDTBEP20',
                'SOL' => 'USDTSOL',
                'TON' => 'USDTTON',
                default => $currency
            };
        }

        // USDC с разными сетями
        if ($currency === 'USDC') {
            return match($network) {
                'ERC20' => 'USDC',
                'BEP20', 'BSC' => 'USDCBEP20',
                'SOL' => 'USDCSOL',
                default => $currency
            };
        }

        // ETH с BSC
        if ($currency === 'ETH') {
            return match($network) {
                'BEP20', 'BSC' => 'ETHBEP20',
                default => 'ETH'
            };
        }

        // BNB
        if ($currency === 'BNB') {
            return 'BNB';
        }

        // Остальные валюты без сети (BTC, LTC, TRX, TON, SOL)
        return $currency;
    }

    /**
     * Генерация HMAC подписи
     */
    private function generateHMAC(int $timestamp, string $data): string
    {
        $message = $timestamp . $data;
        return hash_hmac('sha256', $message, $this->privateKey);
    }

    /**
     * Базовый запрос к WestWallet API
     */
    private function request(string $endpoint, string $method = 'POST', ?array $body = null)
    {
        $timestamp = time();
        $data = $body ? json_encode($body) : '';
        $sign = $this->generateHMAC($timestamp, $data);

        $headers = [
            'Content-Type' => 'application/json',
            'X-API-KEY' => $this->publicKey,
            'X-ACCESS-SIGN' => $sign,
            'X-ACCESS-TIMESTAMP' => (string)$timestamp
        ];

        Log::info('WestWallet API Request', [
            'endpoint' => $endpoint,
            'method' => $method,
            'body' => $body
        ]);

        try {
            if ($method === 'GET' && $body) {
                $response = Http::withHeaders($headers)
                    ->get($this->apiUrl . $endpoint, $body);
            } else {
                $response = Http::withHeaders($headers)
                    ->$method($this->apiUrl . $endpoint, $body);
            }

            $result = $response->json();

            Log::info('WestWallet API Response', [
                'status' => $response->status(),
                'result' => $result
            ]);

            if (isset($result['error']) && $result['error'] !== 'ok') {
                throw new \Exception("WestWallet API Error: " . $result['error']);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('WestWallet API Exception', [
                'error' => $e->getMessage(),
                'endpoint' => $endpoint
            ]);
            throw $e;
        }
    }

    /**
     * Получить список доступных валют (кэш 1 час)
     */
    public function getCurrenciesData()
    {
        return Cache::remember('westwallet_currencies', 3600, function () {
            return $this->request('/wallet/currencies_data', 'GET');
        });
    }

    /**
     * Генерация адреса для платежа
     */
    public function generateAddress(User $user, string $currency, ?string $network): array
    {
        $ticker = $this->getCurrencyTicker($currency, $network);
        $label = UserCryptoWallet::generateLabel($user->id, $currency, $network);
        $ipnUrl = route('westwallet.callback');

        Log::info('🔍 Generating address', [
            'user_id' => $user->id,
            'currency' => $currency,
            'network' => $network,
            'ticker' => $ticker,
            'label' => $label
        ]);

        $response = $this->request('/address/generate', 'POST', [
            'currency' => $ticker,
            'label' => $label,
            'ipn_url' => $ipnUrl
        ]);

        return $response;
    }

    /**
     * Получить существующий адрес или создать новый
     */
    public function getOrCreateWallet(User $user, string $currency, ?string $network): UserCryptoWallet
    {
        // Проверяем существующий адрес
        $wallet = UserCryptoWallet::where('user_id', $user->id)
            ->where('currency', $currency)
            ->where('network', $network)
            ->first();

        if ($wallet) {
            Log::info('📌 Using existing wallet', [
                'user_id' => $user->id,
                'currency' => $currency,
                'network' => $network,
                'address' => $wallet->address
            ]);
            return $wallet;
        }

        // Генерируем новый адрес
        $response = $this->generateAddress($user, $currency, $network);

        if (!isset($response['address'])) {
            throw new \Exception('Failed to generate address');
        }

        // Сохраняем в БД
        $wallet = UserCryptoWallet::create([
            'user_id' => $user->id,
            'currency' => $currency,
            'network' => $network,
            'address' => $response['address'],
            'dest_tag' => $response['dest_tag'] ?? null,
            'label' => UserCryptoWallet::generateLabel($user->id, $currency, $network)
        ]);

        Log::info('✅ Created new wallet', [
            'user_id' => $user->id,
            'currency' => $currency,
            'network' => $network,
            'address' => $wallet->address
        ]);

        return $wallet;
    }

    /**
     * Проверка статуса транзакции
     */
    public function getTransactionStatus(int $transactionId)
    {
        return $this->request('/wallet/transaction', 'POST', [
            'id' => $transactionId
        ]);
    }

    /**
     * Получить баланс кошелька
     */
    public function getBalance(string $currency)
    {
        return $this->request('/wallet/balance', 'POST', [
            'currency' => $currency
        ]);
    }
}