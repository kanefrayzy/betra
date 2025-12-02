<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RateApiService
{
    private static string $openUrl = 'https://open.er-api.com/v6/latest';


    public static function getRate($fromCurrency, $toCurrency)
    {
        $fromCurrency = 'USD';
        $url = self::$openUrl;
        $response = Http::get("{$url}/{$fromCurrency}");

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['rates'][$toCurrency])) {
                return $data['rates'][$toCurrency];
            } else {
                throw new \RuntimeException('Invalid target currency.');
            }
        }

        throw new \RuntimeException('Failed to fetch exchange rate from Open Exchange Rates.');
    }
}
