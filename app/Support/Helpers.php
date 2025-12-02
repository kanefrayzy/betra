<?php


use Illuminate\Support\Facades\Request;

if (!function_exists("host")) {
    function host(): string
    {
        return config('app.url');
    }
}

if (!function_exists('getClientIp')) {
    function getClientIp(): ?string
    {
        $ip = Request::ip();

        if (!$ip || $ip === '127.0.0.1' || $ip === '::1') {
            $ip = Request::header('X-Forwarded-For');
            if ($ip) {
                $ip = explode(',', $ip)[0];
            }
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return null;
        }

        return $ip;
    }
}

if (!function_exists('moneyFormat')) {
    function moneyFormat(float $amount): string
    {
        return number_format($amount, 2, '.', ' ');

    }
}

if (!function_exists('getDevice')) {
    function getDevice(): array|string|null
    {
        return \Illuminate\Support\Facades\Session::has("device_type") ? \Illuminate\Support\Facades\Session::get("device_type") : null;

    }
}


if (!function_exists('toUSD')) {
    function toUSD($amount, $currency): float|int
    {
        return (new \App\Services\ExchangeService())->fromDefaultCurrency($amount, $currency);
    }
}
