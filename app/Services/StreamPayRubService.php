<?php

namespace App\Services;

use App\Interfaces\PaymentSystemInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use DateTime;
use DateTimeZone;
use Exception;

class StreamPayRubService implements PaymentSystemInterface
{
    private $baseUrl;
    private $primaryKey;
    private $storeId;
    private $exchangeService;

    public function __construct()
    {
        $this->baseUrl = Config::get('payment.streampay.base_url', 'https://api.streampay.org');
        $this->primaryKey = hex2bin(Config::get('payment.streampay.primary_key'));
        $this->storeId = Config::get('payment.streampay.store_id', 405);
        $this->exchangeService = new ExchangeService();
    }

    public function createOrder($orderId, $amount, $currency, $systemId)
    {
        try {
            $user = Auth::user();
            $currencySymbol = $user->currency->symbol;

            // Проверяем доступные валюты
            $availableCurrencies = $this->getAvailableCurrencies();
            if (empty($availableCurrencies)) {
                throw new Exception('Failed to get available currencies');
            }

            // Конвертируем сумму в RUB
            $amountRUB = $amount;
            if ($currencySymbol != 'RUB') {
                $amountRUB = $this->exchangeService->convert($amount, $currencySymbol, 'RUB');
            }
            // Получаем информацию о RUB
            $RUBCurrency = collect($availableCurrencies)->firstWhere('code', 'RUB');
            if (!$RUBCurrency) {
                throw new Exception('RUB currency is not available');
            }

            // Проверяем лимиты для RUB
            if ($amountRUB < $RUBCurrency['min_amount'] || $amountRUB > $RUBCurrency['max_amount']) {
                throw new Exception("Amount must be between {$RUBCurrency['min_amount']} and {$RUBCurrency['max_amount']} RUB");
            }

            // Подготавливаем данные для инвойса
            $invoiceData = [
                'amount' => round($amountRUB, $RUBCurrency['precision']),
                'currency' => 'RUB',
                'store_id' => 405,
                'external_id' => (string) $orderId,
                'payment_type' => 1,
                'description' => 'Payment for order #' . $orderId,
                'customer' => (string) $user->id ?? '',
                'system_currency' => 'USDT',
            ];

            // \Log::info('Invoice Data', $invoiceData);

            // Генерируем подпись
            $reqContent = json_encode($invoiceData);
            $utcNow = (new DateTime('now', new DateTimeZone('UTC')))->format('Ymd:Hi');
            $toSign = $reqContent . $utcNow;
            $signature = bin2hex(sodium_crypto_sign_detached($toSign, $this->primaryKey));

            // Создаем инвойс
            $createInvoiceUrl = "{$this->baseUrl}/api/payment/create";
            $headers = [
                'Signature: ' . $signature,
                'Content-Type: application/json',
            ];

            $response = $this->makeRequest('POST', $createInvoiceUrl, $reqContent, $headers);
            $responseData = json_decode($response, true);

            // \Log::info('StreamPay Response', ['response' => $responseData]);


            if (!isset($responseData['data']['pay_url'])) {
                throw new Exception('Invalid response from StreamPay');
            }

            return [
                'error' => false,
                'data' => [
                    'url' => $responseData['data']['pay_url']
                ]
            ];

        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    private function getAvailableCurrencies()
    {
        try {
            $url = "{$this->baseUrl}/api/payment/currencies";
            $response = $this->makeRequest('GET', $url);
            $data = json_decode($response, true);

            if (!isset($data['data']) || !is_array($data['data'])) {
                throw new Exception('Invalid currencies response format');
            }

            return $data['data'];
        } catch (Exception $e) {
            return [];
        }
    }

    private function makeRequest($method, $url, $data = null, $headers = [])
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception('HTTP Request failed: ' . $error);
        }

        return $response;
    }
}
