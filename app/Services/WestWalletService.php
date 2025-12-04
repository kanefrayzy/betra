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

    private function makeRequest(string $endpoint, string $method = 'GET', array $data = []): array
    {
        try {
            $timestamp = time();
            
            // Для GET запросов signature делаем с пустыми данными
            $signatureData = $method === 'GET' ? [] : $data;
            $signature = $this->generateSignature($timestamp, $signatureData);

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
     * Получить правильный тикер валюты с учетом сети
     */
    private function getCurrencyTicker(string $currency, ?string $network = null): string
    {
        $currency = strtoupper($currency);
        
        // Если сеть не указана, возвращаем просто валюту
        if (!$network) {
            return $currency;
        }
        
        $network = strtoupper($network);
        
        // Специальные случаи: когда currency и network совпадают или похожи
        if ($currency === $network || $currency === "{$network}COIN") {
            return $network;
        }
        
        // Маппинг валют и сетей на правильные тикеры WestWallet
        $tickerMap = [
            // USDT на разных сетях
            'USDT' => [
                'ERC20' => 'USDTERC20',
                'TRC20' => 'USDTTRC20',
                'TRC' => 'USDTTRC20',
            ],
            
            // USDC на разных сетях
            'USDC' => [
                'ERC20' => 'USDCERC20',
                'ERC' => 'USDCERC',
                'BEP20' => 'USDCBEP20',
                'BEP' => 'USDCBEP',
            ],
            
            'BITCOIN' => [
                'BTC' => 'BTC',
            ],
            'LITECOIN' => [
                'LTC' => 'LTC',
            ],
            'SOLANA' => [
                'SOL' => 'SOL',
            ],
            'TONCOIN' => [
                'TON' => 'TON',
            ],
            'BNB' => [
                'BNB' => 'BNB',
                'BSC' => 'BNB',
            ],
        ];
        
        if (isset($tickerMap[$currency][$network])) {
            return $tickerMap[$currency][$network];
        }
        
        // Если не нашли в маппинге, пробуем стандартные форматы
        $possibleTickers = [
            $network,                  // Просто сеть (BTC, LTC, SOL, BNB)
            "{$currency}{$network}",   // USDTTRC20, USDCBEP20
            "{$currency}_{$network}",  // USDT_TRC20
        ];
        
        // Проверяем существование в списке валют
        $currenciesData = $this->getCurrenciesData();
        
        foreach ($currenciesData as $currencyData) {
            $tickers = $currencyData['tickers'] ?? [];
            
            foreach ($possibleTickers as $ticker) {
                if (in_array($ticker, $tickers)) {
                    Log::info('Found currency ticker', [
                        'currency' => $currency,
                        'network' => $network,
                        'ticker' => $ticker
                    ]);
                    return $ticker;
                }
            }
        }
        
        return $currency;
    }

    public function getOrCreateWallet(User $user, string $currency, ?string $network = null): array
    {
        // Получаем правильный тикер
        $ticker = $this->getCurrencyTicker($currency, $network);
        
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

    public function generateAddress(User $user, string $currency, ?string $network = null): array
    {
        $label = UserCryptoWallet::generateLabel($user->id, $currency, $network);
        $ipnUrl = route('westwallet.callback');
        
        // Получаем правильный тикер
        $ticker = $this->getCurrencyTicker($currency, $network);

        $data = [
            'currency' => $ticker,
            'ipn_url' => $ipnUrl,
            'label' => $label,
        ];

        $response = $this->makeRequest('/address/generate', 'POST', $data);

        // WestWallet возвращает error как строку или отсутствует при успехе
        if (isset($response['error']) && $response['error'] !== 'ok') {
            Log::error('WestWallet Address Generation Failed', [
                'user_id' => $user->id,
                'currency' => $currency,
                'ticker' => $ticker,
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

        if (!isset($response['address'])) {
            Log::error('WestWallet Address Missing', [
                'user_id' => $user->id,
                'currency' => $currency,
                'ticker' => $ticker,
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
                'network' => $network,
                'ticker' => $ticker,
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

    public function getCurrenciesData(): array
    {
        // Кэшируем на 1 час
        return Cache::remember('westwallet_currencies', 3600, function () {
            $response = $this->makeRequest('/wallet/currencies_data', 'GET', []);
            
            // WestWallet возвращает данные напрямую массивом, а не в поле 'data'
            // Оборачиваем для единообразия с остальными методами
            if (isset($response['error'])) {
                return $response; // Возвращаем ошибку как есть
            }
            
            return $response; // Возвращаем массив валют напрямую
        });
    }

    public function getTransactionStatus(int $transactionId): array
    {
        return $this->makeRequest('/wallet/transaction', 'POST', [
            'id' => $transactionId
        ]);
    }

    public function getTransactionByLabel(string $label): array
    {
        return $this->makeRequest('/wallet/transaction', 'POST', [
            'label' => $label
        ]);
    }

    public function getBalance(string $currency): array
    {
        return $this->makeRequest('/wallet/balance', 'GET', [
            'currency' => strtoupper($currency)
        ]);
    }

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

    public function isCurrencyAvailable(string $currency, ?string $network = null): bool
    {
        $ticker = $this->getCurrencyTicker($currency, $network);
        $currencies = $this->getCurrenciesData();
        
        if (isset($currencies['error'])) {
            return false;
        }

        foreach ($currencies as $currencyData) {
            $tickers = $currencyData['tickers'] ?? [];
            
            if (in_array($ticker, $tickers)) {
                return ($currencyData['active'] ?? false) && ($currencyData['receive_active'] ?? false);
            }
        }

        return false;
    }

    public function getMinDeposit(string $currency, ?string $network = null): ?float
    {
        $ticker = $this->getCurrencyTicker($currency, $network);
        $currencies = $this->getCurrenciesData();
        
        if (isset($currencies['error'])) {
            return null;
        }

        foreach ($currencies as $currencyData) {
            $tickers = $currencyData['tickers'] ?? [];
            
            if (in_array($ticker, $tickers)) {
                return $currencyData['min_receive'] ?? null;
            }
        }

        return null;
    }
}