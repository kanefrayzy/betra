<?php

namespace App\Services;

use App\Interfaces\PaymentSystemInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Services\ExchangeService;

class PayteezService implements PaymentSystemInterface
{
    private $shopId;
    private $secretKey;
    private $currency = 'AZN';
    private $paymentUrl = 'https://payteez.site/api';

    public function __construct()
    {
        $this->shopId = Config::get('payment.payteez.shop_id');
        $this->secretKey = Config::get('payment.payteez.secret_key');
        $this->currency = 'AZN';
    }

    public function createOrder($orderId, $amount, $currency, $systemId = null)
    {
        $user = Auth::user();
        $userCurrency = $user->currency;
        // dd($user->currency->symbol);
        // Если валюты различаются, конвертируем сумму
        $exchangeService = new ExchangeService();
        $amount = $userCurrency->symbol === 'AZN' ? $amount : $exchangeService->convert($amount, $userCurrency->symbol, 'AZN');
        $amount = number_format($amount, 2, '.', '');
        $signature = $this->generatePaymentSignature($this->shopId, $orderId, $amount, $this->currency, $this->secretKey);

        // Тут добавляем ID из нашей плавтежки
        $systemIds = [
            11 => '6',
            12 => '6',
            13 => '8'
        ];

        $params = [
            'shop' => $this->shopId,
            'order' => $orderId,
            'username' => $user->username,
            'amount' => $amount,
            'currency' => $this->currency,
            'signature' => $signature,
            'handler' => 'process',
        ];

        if ($systemId !== null) {
            $params['systemId'] = $systemIds[$systemId];
        }

        $url = $this->paymentUrl . '?' . http_build_query($params);

        return [
            'error' => false,
            'data' => [
                'method' => 'POST',
                'url' => $url,
                'fields' => $params,
            ]
        ];
    }

    private function generatePaymentSignature($shopId, $order, $amount, $currency, $key)
    {
        $data = [$shopId, $order, $amount, $currency, $key];
        $hashString = implode(':', $data);
        $hashedValue = hash('sha256', $hashString);
        return strtoupper($hashedValue);
    }
}
