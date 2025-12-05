<?php

namespace App\Console\Commands;

use App\Services\Slotegrator\SlotegratorClient;
use Illuminate\Console\Command;

class CheckSlotegratorProviders extends Command
{
    protected $signature = 'slotegrator:check-providers';
    protected $description = 'Check which Slotegrator providers are available in your contract';

    public function handle(): void
    {
        $this->info('Checking available Slotegrator providers...');
        
        $client = new SlotegratorClient();
        
        try {
            // Получаем первую страницу игр
            $response = $client->get('/games', ['page' => 1, 'per-page' => 100]);
            
            if (!isset($response['items']) || empty($response['items'])) {
                $this->error('No games found in API response');
                $this->info('This might mean:');
                $this->info('1. No providers are enabled in your contract');
                $this->info('2. API credentials are incorrect');
                $this->info('3. API URL is wrong');
                return;
            }
            
            // Собираем уникальных провайдеров
            $providers = [];
            foreach ($response['items'] as $game) {
                $provider = $game['provider'] ?? 'Unknown';
                if (!isset($providers[$provider])) {
                    $providers[$provider] = 0;
                }
                $providers[$provider]++;
            }
            
            $this->info('Found ' . count($providers) . ' available providers:');
            $this->newLine();
            
            // Выводим таблицу
            $tableData = [];
            foreach ($providers as $provider => $count) {
                $tableData[] = [$provider, $count];
            }
            
            $this->table(['Provider', 'Games Count'], $tableData);
            
            $this->newLine();
            $this->info('Total games on first page: ' . count($response['items']));
            $this->info('Total games available: ' . ($response['_meta']['totalCount'] ?? 'unknown'));
            
            // Попробуем инициализировать первую игру
            $this->newLine();
            $this->info('Testing first game initialization...');
            
            $firstGame = $response['items'][0] ?? null;
            if ($firstGame) {
                $this->info("Game: {$firstGame['name']}");
                $this->info("Provider: {$firstGame['provider']}");
                $this->info("UUID: {$firstGame['uuid']}");
                
                // Здесь можно добавить тестовую инициализацию, но для этого нужен пользователь
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            $this->info('Please check:');
            $this->info('1. SLOTEGRATOR_API_KEY in .env');
            $this->info('2. SLOTEGRATOR_API_ID in .env');
            $this->info('3. SLOTEGRATOR_API_URL in .env');
        }
    }
}
