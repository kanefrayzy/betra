<?php

namespace App\Services;

use App\Interfaces\PaymentSystemInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\ExchangeService;
use Exception;

class BetaTransferService implements PaymentSystemInterface
{
    private $publicKey;
    private $secretKey;
    private $baseUrl = 'https://merchant.betatransfer.io/api';

    public function __construct()
    {
        $this->publicKey = Config::get('payment.betatransfer.public_key');
        $this->secretKey = Config::get('payment.betatransfer.secret_key');
    }

    /**
     * Создание платежа
     */
    public function createOrder($orderId, $amount, $currency, $systemId)
    {
        try {
            $user = Auth::user();
            $userCurrency = $user->currency->symbol;

            // Получаем маппинг платежных систем
            $paymentSystem = $this->getPaymentSystemCode($systemId);
            
            if (!$paymentSystem) {
                throw new Exception('Неподдерживаемая платежная система');
            }

            // Конвертация валюты если необходимо
            $exchangeService = new ExchangeService();
            $processedAmount = $this->processAmount($amount, $userCurrency, $currency, $exchangeService);

            // Формируем параметры запроса В ПРАВИЛЬНОМ ПОРЯДКЕ (важно для подписи!)
            $params = [];
            $params['amount'] = number_format($processedAmount, 2, '.', '');
            $params['currency'] = $currency;
            $params['orderId'] = $orderId;
            $params['paymentSystem'] = $paymentSystem;
            
            // Добавляем опциональные параметры ПЕРЕД служебными URLs
            if ($user->email) {
                $params['payerEmail'] = $user->email;
            }
            if ($user->phone) {
                $params['payerPhone'] = $user->phone;
            }
            
            // Служебные параметры в конце
            $params['urlResult'] = route('betatransfer.callback');
            $params['urlSuccess'] = route('payment.success');
            $params['urlFail'] = route('payment.fail');
            $params['fullCallback'] = '1';
            $params['locale'] = 'ru';

            // Генерируем подпись
            $params['sign'] = $this->generateSign($params);

            Log::debug('BetaTransfer createOrder', [
                'system_id' => $systemId,
                'payment_system' => $paymentSystem,
                'user_currency' => $userCurrency,
                'order_currency' => $currency,
                'amount' => $params['amount']
            ]);

            // Выполняем запрос
            $response = $this->makeRequest('/payment', $params);

            if (isset($response['status']) && $response['status'] === 'success') {
                return [
                    'error' => false,
                    'data' => [
                        'url' => $response['url'] ?? $response['urlPayment'],
                        'transaction_id' => $response['id'],
                        'hash' => $response['hash'],
                    ]
                ];
            }

            throw new Exception($response['message'] ?? 'Ошибка создания платежа');

        } catch (Exception $e) {
            Log::error('BetaTransfer createOrder error: ' . $e->getMessage());
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Создание заявки на вывод
     */
    public function createWithdrawal($orderId, $amount, $currency, $systemId, $details)
    {
        try {
            $user = Auth::user();
            $userCurrency = $user->currency->symbol;

            $paymentSystem = $this->getPaymentSystemCode($systemId);
            
            if (!$paymentSystem) {
                throw new Exception('Неподдерживаемая платежная система для вывода');
            }

            $exchangeService = new ExchangeService();
            $processedAmount = $this->processAmount($amount, $userCurrency, $currency, $exchangeService);

            // Формируем параметры В ПРАВИЛЬНОМ ПОРЯДКЕ (важно для подписи!)
            $params = [];
            $params['amount'] = number_format($processedAmount, 2, '.', '');
            $params['currency'] = $currency;
            $params['orderId'] = $orderId;
            $params['paymentSystem'] = $paymentSystem;
            $params['address'] = $details;

            // Добавляем дополнительные поля для получателя
            if ($user->first_name && $user->last_name) {
                $params['receiverFirstName'] = $user->first_name;
                $params['receiverLastName'] = $user->last_name;
            }
            if ($user->email) {
                $params['receiverEmail'] = $user->email;
            }
            if ($user->phone) {
                $params['receiverPhone'] = $user->phone;
            }
            
            // URL в конце
            $params['urlResult'] = route('betatransfer.callback');

            $params['sign'] = $this->generateSign($params);

            Log::debug('BetaTransfer createWithdrawal', [
                'system_id' => $systemId,
                'payment_system' => $paymentSystem,
                'user_currency' => $userCurrency,
                'order_currency' => $currency,
                'amount' => $params['amount']
            ]);

            $response = $this->makeRequest('/withdrawal-payment', $params);

            if (isset($response['status']) && $response['status'] === 'success') {
                return [
                    'error' => false,
                    'data' => [
                        'transaction_id' => $response['id'],
                        'order_id' => $response['orderId'],
                    ]
                ];
            }

            throw new Exception($response['message'] ?? 'Ошибка создания вывода');

        } catch (Exception $e) {
            Log::error('BetaTransfer createWithdrawal error: ' . $e->getMessage());
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Получение информации о транзакции
     */
    public function getTransactionInfo($transactionId = null, $orderId = null)
    {
        try {
            if (!$transactionId && !$orderId) {
                throw new Exception('Необходим либо transactionId, либо orderId');
            }

            $params = [];
            if ($transactionId) {
                $params['id'] = $transactionId;
            }
            if ($orderId) {
                $params['orderId'] = $orderId;
            }

            $params['sign'] = $this->generateSign($params);

            return $this->makeRequest('/info', $params);

        } catch (Exception $e) {
            Log::error('BetaTransfer getTransactionInfo error: ' . $e->getMessage());
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Проверка подписи callback
     */
    public function verifyCallbackSignature($amount, $orderId, $sign)
    {
        $expectedSign = md5($amount . $orderId . $this->secretKey);
        return $sign === $expectedSign;
    }

    /**
     * Генерация подписи для запроса
     */
    private function generateSign(array $params)
    {
        // Убираем sign если он уже есть
        unset($params['sign']);
        
        // Склеиваем все значения параметров
        $signString = implode('', $params) . $this->secretKey;
        
        Log::debug('BetaTransfer Sign Generation', [
            'params' => $params,
            'sign_string' => $signString,
            'sign' => md5($signString)
        ]);
        
        return md5($signString);
    }

    /**
     * Выполнение HTTP запроса
     */
    private function makeRequest($endpoint, array $params, $method = 'POST')
    {
        $url = $this->baseUrl . $endpoint . '?token=' . $this->publicKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($method === 'POST' && !empty($params)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200) {
            Log::error('BetaTransfer API Error', [
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'response' => $response,
                'endpoint' => $endpoint,
                'params' => $params
            ]);
            throw new Exception("HTTP Error: $httpCode - Response: $response");
        }

        $decoded = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Ошибка декодирования JSON ответа');
        }

        return $decoded;
    }

    /**
     * Обработка суммы с конвертацией валюты
     */
    private function processAmount($amount, $fromCurrency, $toCurrency, ExchangeService $exchangeService)
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        return $exchangeService->convert($amount, $fromCurrency, $toCurrency);
    }

    /**
     * Маппинг ID payment_handlers на коды BetaTransfer API
     * ID берутся из таблицы payment_handlers
     * 
     * ДВЕ СТРАТЕГИИ:
     * 1. Для RUB - используем конкретный systemId (Card RUB id=7, СБП id=8)
     * 2. Для остальных - определяем по валюте пользователя автоматически
     * 
     * Доступные системы:
     * P2R (Card RUB), Card5 (СБП), P2R_KZT, P2R_AZN, P2R_UZS, P2R_TJS, P2R_KGS
     */
    private function getPaymentSystemCode($systemId)
    {
        // Получаем валюту текущего пользователя
        $user = Auth::user();
        $userCurrency = $user ? $user->currency->symbol : null;

        // ПРИОРИТЕТ 1: Точный маппинг по systemId (для RUB и других специфичных систем)
        $exactMapping = [
            // RU - две разные системы для рублей
            7 => 'P2R',            // RU Card P2P (Visa/MC/МИР) - min 1000, max 100000 RUB
            8 => 'Card5',          // RU СБП по номеру телефона - min 1000, max 100000 RUB
            
            // Остальные страны (если нужен точный выбор)
            21 => 'P2R_KZT',       // KZ Card
            22 => 'P2R_UZS',       // UZ Card
            23 => 'P2R_UZS',       // UZ HUMO
            24 => 'P2R_UZS',       // UZ Payme
            25 => 'P2R_AZN',       // AZ Card
            26 => 'P2R_AZN',       // AZ M10
            27 => 'P2R_AZN',       // AZ Acquiring
            34 => 'P2R_TJS',       // TJ Card
            35 => 'P2R_KGS',       // KG Card
            
            // Crypto - закомментировано (пока не используется)
            // 29 => 'USD_TRC20',  // USDT TRC20 вывод
            // 30 => 'BTC',        // Bitcoin
            // 31 => 'ETH',        // Ethereum
            // 32 => 'TRX',        // TRON
            // 33 => 'LTC',        // Litecoin
        ];

        // Если есть точное соответствие - используем его
        if (isset($exactMapping[$systemId])) {
            return $exactMapping[$systemId];
        }

        // ПРИОРИТЕТ 2: Автоматический выбор по валюте пользователя
        $currencyMapping = [
            'RUB' => 'P2R',        // Россия: 1000-100000 RUB (Card по умолчанию)
            'KZT' => 'P2R_KZT',    // Казахстан: 4000-200000 KZT, комиссия 8.5%
            'AZN' => 'P2R_AZN',    // Азербайджан: 10-5000 AZN, комиссия 9%
            'UZS' => 'P2R_UZS',    // Узбекистан: 30000-5700000 UZS, комиссия 4%
            'TJS' => 'P2R_TJS',    // Таджикистан: 100-10000 TJS, комиссия 8.5%
            'KGS' => 'P2R_KGS',    // Кыргызстан: 500-150000 KGS, комиссия 9%
        ];

        // Если валюта пользователя есть в маппинге - используем её
        if ($userCurrency && isset($currencyMapping[$userCurrency])) {
            return $currencyMapping[$userCurrency];
        }

        return null;
    }

    /**
     * Получение баланса аккаунта
     */
    public function getAccountInfo()
    {
        try {
            $sign = md5($this->publicKey . $this->secretKey);
            $url = $this->baseUrl . '/account-info?token=' . $this->publicKey . '&sign=' . $sign;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            curl_close($ch);

            return json_decode($response, true);

        } catch (Exception $e) {
            Log::error('BetaTransfer getAccountInfo error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Получение списка доступных платежных систем
     * GET /payment-system
     */
    public function getAvailablePaymentSystems()
    {
        try {
            $sign = md5($this->publicKey . $this->secretKey);
            $url = $this->baseUrl . '/payment-system?token=' . $this->publicKey . '&sign=' . $sign;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($response, true);
                return $data ?: [];
            }

            Log::warning('BetaTransfer payment-system API returned non-200', [
                'http_code' => $httpCode,
                'response' => $response
            ]);
            
            return [];

        } catch (Exception $e) {
            Log::error('BetaTransfer getAvailablePaymentSystems error: ' . $e->getMessage());
            return [];
        }
    }

}
