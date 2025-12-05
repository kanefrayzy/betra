<?php

namespace App\Console\Commands;

use App\Models\SlotegratorGame;
use App\Services\Slotegrator\SlotegratorClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ImportSlotegratorGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slotegrator:import';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import games data from Slotegrator API';


    public function handle(): void
    {
        $this->info('Starting Slotegrator games import...');
        
        $client = new SlotegratorClient();
        $cacheKey = 'games_list';

        $allGames = [];
        $currentPage = 1;
        $perPage = 100; // Увеличим до 100 для более быстрого импорта

        try {
            $this->info('Fetching games from Slotegrator API...');
            
            // Сначала получаем первую страницу чтобы узнать общее количество
            $firstResponse = $client->get('/games', [
                'page' => 1,
                'per-page' => $perPage
            ]);
            
            if (!isset($firstResponse['items']) || !is_array($firstResponse['items'])) {
                $this->error('Invalid response format from API');
                $this->error('Response: ' . json_encode($firstResponse));
                $this->newLine();
                $this->warn('This might mean no providers are enabled in your contract.');
                $this->warn('Please contact Slotegrator support to enable providers.');
                return;
            }
            
            $totalPages = $firstResponse['_meta']['pageCount'] ?? 1;
            $totalGames = $firstResponse['_meta']['totalCount'] ?? 0;
            
            $this->info("Total games available: {$totalGames}");
            $this->info("Total pages: {$totalPages}");
            $this->newLine();
            
            $allGames = $firstResponse['items'];
            $this->info("Page 1/{$totalPages}: " . count($firstResponse['items']) . " games fetched");
            
            // Получаем остальные страницы
            for ($currentPage = 2; $currentPage <= $totalPages; $currentPage++) {
                $this->info("Fetching page {$currentPage}/{$totalPages}...");
                
                $response = $client->get('/games', [
                    'page' => $currentPage,
                    'per-page' => $perPage
                ]);
                
                if (isset($response['items']) && is_array($response['items'])) {
                    $gamesOnPage = $response['items'];
                    $allGames = array_merge($allGames, $gamesOnPage);
                    $this->info("Page {$currentPage}/{$totalPages}: " . count($gamesOnPage) . " games fetched");
                }
                
                // Небольшая задержка чтобы не перегружать API
                usleep(100000); // 0.1 секунды
            }

            $this->newLine();
            $this->info('Total games fetched: ' . count($allGames));
            
            $imported = 0;
            $updated = 0;
            $skipped = 0;

            $this->info('Importing games to database...');
            $progressBar = $this->output->createProgressBar(count($allGames));
            $progressBar->start();

            foreach ($allGames as $gameData) {
                // Пропускаем мобильные версии если есть десктопная
                if (Str::contains($gameData['name'], 'Mobile')) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                try {
                    // Проверяем существование игры и обновляем если нужно
                    $existingGame = SlotegratorGame::where('uuid', $gameData['uuid'])->first();
                    
                    if ($existingGame) {
                        // Обновляем данные существующей игры
                        $existingGame->update([
                            'name' => $gameData['name'],
                            'image' => !empty($gameData['image']) ? $gameData['image'] : '/assets/images/avatar-placeholder.png',
                            'type' => $gameData['type'] ?? null,
                            'provider' => $gameData['provider'] ?? null,
                            'technology' => $gameData['technology'] ?? null,
                            'has_lobby' => $gameData['has_lobby'] ?? 0,
                            'is_mobile' => $gameData['is_mobile'] ?? 0,
                            'has_freespins' => $gameData['has_freespins'] ?? 0,
                            'has_tables' => $gameData['has_tables'] ?? 0,
                            'freespin_valid_until_full_day' => $gameData['freespin_valid_until_full_day'] ?? 0,
                        ]);
                        $updated++;
                    } else {
                        // Создаем новую игру
                        SlotegratorGame::create([
                            'uuid' => $gameData['uuid'],
                            'name' => $gameData['name'],
                            'image' => !empty($gameData['image']) ? $gameData['image'] : '/assets/images/avatar-placeholder.png',
                            'type' => $gameData['type'] ?? null,
                            'provider' => $gameData['provider'] ?? null,
                            'technology' => $gameData['technology'] ?? null,
                            'has_lobby' => $gameData['has_lobby'] ?? 0,
                            'is_mobile' => $gameData['is_mobile'] ?? 0,
                            'has_freespins' => $gameData['has_freespins'] ?? 0,
                            'has_tables' => $gameData['has_tables'] ?? 0,
                            'freespin_valid_until_full_day' => $gameData['freespin_valid_until_full_day'] ?? 0,
                        ]);
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $this->error("\nFailed to import game {$gameData['name']}: " . $e->getMessage());
                }
                
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            Cache::put($cacheKey, $allGames, now()->addDay());

            $this->info("Games import completed!");
            $this->info("New games imported: {$imported}");
            $this->info("Games updated: {$updated}");
            $this->info("Skipped (mobile versions): {$skipped}");
            
            // Показываем статистику по провайдерам
            $this->newLine();
            $providers = SlotegratorGame::select('provider')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('provider')
                ->orderByDesc('count')
                ->get();
            
            $this->info('Games by provider:');
            $tableData = [];
            foreach ($providers as $provider) {
                $tableData[] = [$provider->provider, $provider->count];
            }
            $this->table(['Provider', 'Games'], $tableData);
            
        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
        }
    }
}
