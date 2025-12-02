<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearGameCaches extends Command
{
    protected $signature = 'cache:clear-games';
    protected $description = 'Clear all game related caches';

    public function handle()
    {
        Cache::forget('popular_games');
        Cache::forget('live_games');
        Cache::forget('roulette_games');
        Cache::forget('table_games');

        // Очистка кэшей для игр с повышенными ставками
        $this->clearHigherGameCaches();

        $this->info('All game caches have been cleared!');
    }

    private function clearHigherGameCaches()
    {
        $keys = Cache::get('higher_game_cache_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget('higher_game_cache_keys');
    }
}
