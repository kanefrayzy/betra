<?php
namespace App\Console\Commands;
use App\Models\Currency;
use App\Models\Rate;
use App\Services\RateApiService;
use Illuminate\Console\Command;
use App\Services\ExchangeService;
use Illuminate\Support\Facades\Log;

class GetCurrencyRates extends Command
{
    protected $signature = 'rate:get';
    protected $description = 'Update currency rates';

    protected $exchangeService;

    public function __construct(ExchangeService $exchangeService)
    {
        parent::__construct();
        $this->exchangeService = $exchangeService;
    }

    public function handle(): void
    {
        $defaultCurrency = env('DEFAULT_CURRENCY');
        $currencies = Currency::where('symbol', '!=', $defaultCurrency)->get();
        foreach ($currencies as $currency) {
            $rate = RateApiService::getRate($defaultCurrency, $currency->symbol);
            $currency->rate()->updateOrCreate([], ['price' => $rate]);
        }
        Log::info("Currency rates fetched");

        $this->info('Refreshing currency rates cache...');
        try {
            $this->exchangeService->refreshRates();
            $this->info('Currency rates cache has been successfully refreshed.');
        } catch (\Exception $e) {
            $this->error('An error occurred while refreshing the currency rates cache: ' . $e->getMessage());
        }
    }
}
