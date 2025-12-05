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
        $perPage = 50;

        try {
            $this->info('Fetching games from Slotegrator API...');
            
            do {
                $this->info("Fetching page {$currentPage}...");
                
                $response = $client->get('/games', [
                    'page' => $currentPage,
                    'per-page' => $perPage
                ]);
                
                if (!isset($response['items']) || !is_array($response['items'])) {
                    $this->error('Invalid response format from API');
                    $this->error('Response: ' . json_encode($response));
                    return;
                }
                
                $gamesOnPage = $response['items'];
                $allGames = array_merge($allGames, $gamesOnPage);
                
                $this->info("Page {$currentPage}: " . count($gamesOnPage) . " games fetched");
                
                $currentPage++;
            } while ($currentPage <= ($response['_meta']['pageCount'] ?? 1));

            $this->info('Total games fetched: ' . count($allGames));
            
            $imported = 0;
            $skipped = 0;

            foreach ($allGames as $gameData) {
                // Пропускаем мобильные версии если есть десктопная
                if (Str::contains($gameData['name'], 'Mobile')) {
                    $skipped++;
                    continue;
                }

                // Проверяем существование игры
                if (SlotegratorGame::where('uuid', $gameData['uuid'])->exists()) {
                    $skipped++;
                    continue;
                }

                try {
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
                } catch (\Exception $e) {
                    $this->error("Failed to import game {$gameData['name']}: " . $e->getMessage());
                }
            }

            Cache::put($cacheKey, $allGames, now()->addDay());

            $this->info("Games import completed!");
            $this->info("Imported: {$imported}");
            $this->info("Skipped: {$skipped}");
            
        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
        }
    }
}
