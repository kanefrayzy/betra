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
        $client = new SlotegratorClient();
        $cacheKey = 'games_list';


        $allGames = [];
        $currentPage = 1;
        $perPage = 50;

        do {
            $response = $client->get('/games', ['page' => $currentPage]);
            $gamesOnPage = $response['items'];
            $allGames = array_merge($allGames, $gamesOnPage);
            $currentPage++;
        } while ($currentPage <= $response['_meta']['pageCount']);

        foreach ($allGames as $gameData) {

            if (Str::contains($gameData['name'], 'Mobile')) {
                continue;
            }

            if (SlotegratorGame::where('uuid', $gameData['uuid'])->exists()) {
                continue;
            }

            SlotegratorGame::create([
                'uuid' => $gameData['uuid'],
                'name' => $gameData['name'],
                'image' => $gameData['image'] ?? null,
                'type' => $gameData['type'] ?? null,
                'provider' => $gameData['provider'] ?? null,
                'technology' => $gameData['technology'] ?? null,
                'has_lobby' => $gameData['has_lobby'] ?? 0,
                'is_mobile' => $gameData['is_mobile'] ?? 0,
                'has_freespins' => $gameData['has_freespins'] ?? 0,
                'has_tables' => $gameData['has_tables'] ?? 0,
                'freespin_valid_until_full_day' => $gameData['freespin_valid_until_full_day'] ?? 0,
            ]);
        }

        Cache::put($cacheKey, $allGames, now()->addDay());

        $this->info('Games data imported successfully!');
    }
}
