<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ExchangeService
{
    protected string $defaultCurrency;
    protected int $cacheTime = 3600; // 1 час в секундах

    public function __construct()
    {
        $this->defaultCurrency = config('app.currency');
    }

    public function convert($amount, $from, $to): float|int
    {
        $amountInDefaultCurrency = $this->toDefaultCurrency($amount, $from);
        if ($to === $this->defaultCurrency) {
            return $amountInDefaultCurrency;
        }
        return $this->fromDefaultCurrency($amountInDefaultCurrency, $to);
    }

    public function toDefaultCurrency($amount, $from): float|int
    {
      if ($from === $this->defaultCurrency) {
          return is_null($amount) ? 0 : (float)$amount;
      }

        $rate = $this->getCachedRate($from);
        return $amount / $rate;
    }

    public function fromDefaultCurrency($amount, $to): float|int
    {

        if (is_null($amount) || !is_numeric($amount)) {
            return 0;
        }

        $amount = (float)$amount;

        if ($to === $this->defaultCurrency) {
            return $amount;
        }

        $rate = $this->getCachedRate($to);
        if (is_null($rate) || !is_numeric($rate)) {
            return 0;
        }

        $rate = (float)$rate;

        return $amount * $rate;
    }

    protected function getCachedRate(string $symbol): float
    {
        return Cache::remember("currency_rate:{$symbol}", $this->cacheTime, function () use ($symbol) {
            $currency = Currency::with('rate')->where('symbol', $symbol)->first();

            if (!$currency || !$currency->rate) {
                Log::error("Currency rate for {$symbol} not found.");
                throw new \Exception("Currency rate for {$symbol} not found.");
            }

            return $currency->rate->price;
        });
    }

    public function refreshRates(): void
    {
        $currencies = Currency::with('rate')->get();

        foreach ($currencies as $currency) {
            if ($currency->rate) {
                Cache::put("currency_rate:{$currency->symbol}", $currency->rate->price, $this->cacheTime);
            } else {
                Log::warning("No rate found for currency: {$currency->symbol}");
            }
        }
    }
}
