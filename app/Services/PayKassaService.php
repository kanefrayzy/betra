<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Interfaces\PaymentSystemInterface;
use Paykassa\PaykassaSCI;
use App\Services\ExchangeService;

class PayKassaService implements PaymentSystemInterface
{
    private $paykassa;

    public function __construct()
    {
        $this->paykassa = new PaykassaSCI(
            config('payment.paykassa.merchant_id'),
            config('payment.paykassa.merchant_key'),
            config('payment.paykassa.test_mode', false)
        );
    }

    public function createOrder($orderId, $amount, $currency, $systemId = null)
    {
        try {
          // Проверяем валюту пользователя
          $user = Auth::user();
          $userCurrency = $user->currency;
          // dd($user->currency->symbol);
          // Если валюты различаются, конвертируем сумму
              $exchangeService = new ExchangeService();
              $amount = $userCurrency->symbol === 'USD' ? $amount : $exchangeService->convert($amount, $userCurrency->symbol, 'USD');

              if($currency != "USDT"){
                $amount = $this->convertToUSD($amount, $currency);
              }
                $systemId = $this->getPayKassaSystem($currency);
                $systemName = $this->getSystemNameById($systemId);
                $comment = 'Deposit';
                // dd($amount);
                  $response = $this->paykassa->createOrder(
                  $amount,
                  $systemName,
                  $currency,
                  $orderId,
                  $comment
              );

            if ($response['error']) {
                throw new \Exception($response['message']);
            }
            \Log::info("Creating order with params", compact('amount', 'systemId', 'currency', 'orderId', 'comment'));

            return [
                'error' => false,
                'data' => $response['data']
            ];
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    protected function convertToUSD($amount, $currency)
    {
        $conversionRates = [
            "BTC" => 'BTCUSDT',
            "BNB" => 'BNBUSDT',
            "TRX" => 'TRXUSDT',
            "LTC" => 'LTCUSDT',
            "XRP" => 'XRPUSDT',
            "DOGE" => 'DOGEUSDT',
        ];

        if (array_key_exists($currency, $conversionRates)) {
            $symbol = $conversionRates[$currency];
            $ch = curl_init("https://api.binance.com/api/v3/ticker/price?symbol={$symbol}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $html = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($html, true);

            if (isset($data['price'])) {
                return $amount / $data['price'];
            }
        }

        return $amount; // Return the original amount if no conversion is available
    }

     private function getSystemNameById($systemId)
     {
         $systems = PaykassaSCI::getPaymentSystems();
         foreach ($systems as $name => $system) {
             if ($system['system_id'] == $systemId) {
                 return $system['system'];
             }
         }
         throw new \Exception("Неподдерживаемая платежная система с ID: $systemId");
     }

     private function getPayKassaSystem($currencyCode)
     {
         $systems = [
             'USD' => 7,
             'RUB' => 7,
             'BTC' => 11,
             'ETH' => 12,
             'LTC' => 14,
             'DOGE' => 15,
             'DASH' => 16,
             'BCH' => 18,
             'ETC' => 21,
             'XRP' => 22,
             'TRX' => 27,
             'XLM' => 28,
             'BNB' => 29,
             'USDT' => 30,
             'USDT_BEP20' => 31,
             'BUSD_BEP20' => 31,
             'USDC_BEP20' => 31,
             'ADA_BEP20' => 31,
             'EOS_BEP20' => 31,
             'BTC_BEP20' => 31,
             'ETH_BEP20' => 31,
             'DOGE_BEP20' => 31,
             'SHIB_BEP20' => 31,
             'USDT_ERC20' => 32,
             'BUSD_ERC20' => 32,
             'USDC_ERC20' => 32,
             'SHIB_ERC20' => 32,
         ];

         return $systems[$currencyCode] ?? null;
     }
 }
