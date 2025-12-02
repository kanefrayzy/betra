<?php

namespace App\Services;

use App\Interfaces\PaymentSystemInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Services\ExchangeService;

class FreeKassaService implements PaymentSystemInterface
{
    private $merchantId;
    private $secretKey;
    private $paymentUrl = 'https://pay.freekassa.com/';

    public function __construct()
    {
        $this->merchantId = Config::get('payment.freekassa.merchant_id');
        $this->secretKey = Config::get('payment.freekassa.secret_key');
    }

    public function createOrder($orderId, $amount, $currency, $systemId)
    {
            $mapping = [
                7 => 4,
                8 => 42,
                9 => 12,
                10 => 6,
            ];

          $sysId = $mapping[$systemId] ?? $systemId;
          $user = Auth::user();
          $userCurrency = $user->currency;
          // dd($user->currency->symbol);
          // Если валюты различаются, конвертируем сумму
        $exchangeService = new ExchangeService();
        $amount = $userCurrency->symbol === 'RUB' ? $amount : $exchangeService->convert($amount, $userCurrency->symbol, 'RUB');
        $signature = $this->generateSignature($amount, $currency, $orderId);

        $params = [
            'm' => $this->merchantId,
            'oa' => $amount,
            'o' => $orderId,
            's' => $signature,
            'i' => $sysId,
            'em' => $orderId . '@flashgame.live',
            'currency' => $currency,
            'lang' => 'ru', // или 'en', в зависимости от вашего выбора
        ];

        // Добавляем дополнительные параметры, если они нужны
        // $params['i'] = $preferredCurrency;
        // $params['phone'] = $userPhone;
        // $params['em'] = $userEmail;

        $url = $this->paymentUrl . '?' . http_build_query($params);

        return [
            'error' => false,
            'data' => [
                'url' => $url,
            ]
        ];
    }

    private function generateSignature($amount, $currency, $orderId)
    {
        return md5($this->merchantId . ':' . $amount . ':' . $this->secretKey . ':' . $currency . ':' . $orderId);
    }
}
