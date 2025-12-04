<?php

namespace App\Console\Commands;

use App\Models\SlotegratorGame;
use App\Services\B2BSlotsService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportB2bGamesCommand extends Command
{
    protected $signature = 'b2b:import {--provider= : Filter by provider name} {--limit= : Limit number of games to import}';

    protected $description = 'Import games from B2B Slots API';

    protected B2BSlotsService $b2bService;

    public function __construct(B2BSlotsService $b2bService)
    {
        parent::__construct();
        $this->b2bService = $b2bService;
    }

    public function handle(): int
    {
        $this->info('🚀 Starting B2B Slots games import...');

        $apiUrl = config('services.b2b_slots.api_url');
        $partnerId = config('services.b2b_slots.partner_id');

        if (!$apiUrl || !$partnerId) {
            $this->error('❌ B2B Slots credentials not configured in config/services.php');
            return self::FAILURE;
        }

        // Если partner_id = 0, используем предопределенный список
        if ($partnerId == 0 || $partnerId === '0') {
            $this->warn('⚠️  Partner ID is 0, using predefined games list');
            return $this->importPredefinedGames();
        }

        $providerFilter = $this->option('provider');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        try {
            $this->info('📡 Fetching games from B2B Slots API...');

            // Получаем список игр через сервис
            $allGames = $this->b2bService->getGames();

            if (empty($allGames)) {
                $this->warn('⚠️  No games received from API');
                $this->warn('Check Laravel logs for more details');
                $this->info('Falling back to predefined games list...');
                return $this->importPredefinedGames();
            }

            $this->info('📦 Total games fetched: ' . count($allGames));

            // Применяем фильтры
            if ($providerFilter) {
                $allGames = array_filter($allGames, function($game) use ($providerFilter) {
                    return isset($game['provider']) && 
                           stripos($game['provider'], $providerFilter) !== false;
                });
                $this->info('🔍 After provider filter: ' . count($allGames) . ' games');
            }

            if ($limit) {
                $allGames = array_slice($allGames, 0, $limit);
                $this->info('🔢 Limited to: ' . count($allGames) . ' games');
            }

            $this->newLine();
            $progressBar = $this->output->createProgressBar(count($allGames));
            $progressBar->start();

            $importedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;

            foreach ($allGames as $gameData) {
                // Пропускаем игры без обязательных полей
                if (!isset($gameData['game_code']) || !isset($gameData['name'])) {
                    $skippedCount++;
                    $progressBar->advance();
                    continue;
                }

                // Проверяем существует ли игра
                $existingGame = SlotegratorGame::where('game_code', $gameData['game_code'])
                    ->where('provider_type', 'b2b_slots')
                    ->first();

                if ($existingGame) {
                    // Обновляем существующую игру
                    $existingGame->update([
                        'name' => $gameData['name'],
                        'image' => $gameData['image'] ?? $existingGame->image,
                        'is_mobile' => $gameData['is_mobile'] ?? $existingGame->is_mobile,
                    ]);
                    $updatedCount++;
                } else {
                    // Создаем игру
                    SlotegratorGame::create([
                        'uuid' => Str::uuid(),
                        'name' => $gameData['name'],
                        'game_code' => $gameData['game_code'],
                        'provider' => $gameData['provider'] ?? 'Unknown',
                        'provider_type' => 'b2b_slots',
                        'type' => $gameData['type'] ?? 'slot',
                        'technology' => 'html5',
                        'has_lobby' => false,
                        'is_mobile' => $gameData['is_mobile'] ?? true,
                        'has_freespins' => false,
                        'has_tables' => false,
                        'freespin_valid_until_full_day' => false,
                        'is_active' => true,
                        'image' => $gameData['image'] ?? null,
                    ]);

                    $importedCount++;
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            $this->info("✅ Import completed!");
            $this->info("📊 Statistics:");
            $this->info("   - New games imported: {$importedCount}");
            $this->info("   - Updated games: {$updatedCount}");
            $this->info("   - Skipped (invalid data): {$skippedCount}");
            $this->info("   - Total processed: " . count($allGames));

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return self::FAILURE;
        }
    }

    protected function importPredefinedGames(): int
    {
        $this->info('📦 Importing predefined B2B games list...');

        // Список популярных игр
        $games = [
            // NETENT
            ['name' => 'Starburst', 'game_code' => 1012, 'provider' => 'NETENT'],
            ['name' => 'Gonzo\'s Quest', 'game_code' => 1039, 'provider' => 'NETENT'],
            ['name' => 'Dead or Alive', 'game_code' => 1023, 'provider' => 'NETENT'],
            ['name' => 'Fruit Shop', 'game_code' => 1003, 'provider' => 'NETENT'],
            ['name' => 'Twin Spin', 'game_code' => 1063, 'provider' => 'NETENT'],
            ['name' => 'Piggy Riches', 'game_code' => 1001, 'provider' => 'NETENT'],
            ['name' => 'Stickers', 'game_code' => 1002, 'provider' => 'NETENT'],
            ['name' => 'Flowers', 'game_code' => 1005, 'provider' => 'NETENT'],
            ['name' => 'Aloha', 'game_code' => 1054, 'provider' => 'NETENT'],
            ['name' => 'Red Riding Hood', 'game_code' => 1069, 'provider' => 'NETENT'],
            ['name' => 'Hansel & Gretel', 'game_code' => 1068, 'provider' => 'NETENT'],

            // NOVOMATIC
            ['name' => 'Book of Ra Deluxe', 'game_code' => 14, 'provider' => 'NOVOMATIC DELUXE'],
            ['name' => 'Sizzling Hot Deluxe', 'game_code' => 92, 'provider' => 'NOVOMATIC DELUXE'],
            ['name' => 'Lucky Lady\'s Charm Deluxe', 'game_code' => 60, 'provider' => 'NOVOMATIC DELUXE'],
            ['name' => 'Dolphin\'s Pearl Deluxe', 'game_code' => 26, 'provider' => 'NOVOMATIC DELUXE'],
            ['name' => 'Just jewels', 'game_code' => 54, 'provider' => 'NOVOMATIC DELUXE'],
            ['name' => 'Fruit farm', 'game_code' => 34, 'provider' => 'NOVOMATIC DELUXE'],
            ['name' => 'Helena', 'game_code' => 47, 'provider' => 'NOVOMATIC DELUXE'],
            ['name' => 'Sea sirens', 'game_code' => 86, 'provider' => 'NOVOMATIC DELUXE'],
            ['name' => 'Cryptic highway', 'game_code' => 21, 'provider' => 'NOVOMATIC DELUXE'],
            ['name' => 'Just jewels deluxe', 'game_code' => 55, 'provider' => 'NOVOMATIC DELUXE'],

            // Play'n GO
            ['name' => 'Book of Dead', 'game_code' => 3004, 'provider' => 'PlaynGO'],
            ['name' => 'Reactoonz', 'game_code' => 3007, 'provider' => 'PlaynGO'],
            ['name' => 'Moon Princess', 'game_code' => 3008, 'provider' => 'PlaynGO'],
            ['name' => 'Jewel Box', 'game_code' => 3000, 'provider' => 'PlaynGO'],
            ['name' => 'Lady Of Fortune', 'game_code' => 3001, 'provider' => 'PlaynGO'],
            ['name' => 'Samba Carnival', 'game_code' => 3002, 'provider' => 'PlaynGO'],
            ['name' => 'Merry Xmas', 'game_code' => 3003, 'provider' => 'PlaynGO'],
            ['name' => 'Doom of Egypt', 'game_code' => 3005, 'provider' => 'PlaynGO'],
            ['name' => 'Rise Of Merlin', 'game_code' => 3006, 'provider' => 'PlaynGO'],

            // YGGDRASIL
            ['name' => 'Rainbow Ryan', 'game_code' => 2001, 'provider' => 'YGGDRASIL'],
            ['name' => 'Sunny Shores', 'game_code' => 2014, 'provider' => 'YGGDRASIL'],
            ['name' => 'Vikings Go Berzerk', 'game_code' => 2024, 'provider' => 'YGGDRASIL'],
            ['name' => 'Double Dragons', 'game_code' => 2023, 'provider' => 'YGGDRASIL'],
            ['name' => 'Gem Rocks', 'game_code' => 2022, 'provider' => 'YGGDRASIL'],
            ['name' => 'Easter Island', 'game_code' => 2021, 'provider' => 'YGGDRASIL'],
            ['name' => 'Pumpkin Smash', 'game_code' => 2019, 'provider' => 'YGGDRASIL'],

            // Pragmatic Play
            ['name' => 'Sweet Bonanza', 'game_code' => 4010, 'provider' => 'Pragmatic Play'],
            ['name' => 'The Dog House', 'game_code' => 4011, 'provider' => 'Pragmatic Play'],
            ['name' => 'Gates of Olympus', 'game_code' => 4012, 'provider' => 'Pragmatic Play'],
            ['name' => 'Wolf Gold', 'game_code' => 4013, 'provider' => 'Pragmatic Play'],
            ['name' => 'Book Of Tut', 'game_code' => 4000, 'provider' => 'Pragmatic Play'],
            ['name' => 'Book of Vikings', 'game_code' => 4001, 'provider' => 'Pragmatic Play'],
            ['name' => 'Return of the Dead', 'game_code' => 4002, 'provider' => 'Pragmatic Play'],
            ['name' => 'Scarab Queen', 'game_code' => 4003, 'provider' => 'Pragmatic Play'],
            ['name' => 'Heart of Rio', 'game_code' => 4004, 'provider' => 'Pragmatic Play'],
            ['name' => 'Madame Destiny', 'game_code' => 4005, 'provider' => 'Pragmatic Play'],
            ['name' => 'Ancient Egypt Classic', 'game_code' => 4006, 'provider' => 'Pragmatic Play'],

            // PUSH GAMING
            ['name' => 'Hearts Highway', 'game_code' => 5000, 'provider' => 'PUSH GAMING'],
            ['name' => 'Fat Rabbit', 'game_code' => 5001, 'provider' => 'PUSH GAMING'],
            ['name' => 'Fat Santa', 'game_code' => 5002, 'provider' => 'PUSH GAMING'],
            ['name' => '10 Swords', 'game_code' => 5003, 'provider' => 'PUSH GAMING'],
            ['name' => 'Fire Hopper', 'game_code' => 5005, 'provider' => 'PUSH GAMING'],
            ['name' => 'Blaze Of Ra', 'game_code' => 5006, 'provider' => 'PUSH GAMING'],
            ['name' => 'Razor Shark', 'game_code' => 5007, 'provider' => 'PUSH GAMING'],

            // BetInHell
            ['name' => 'Book Of Sunrise', 'game_code' => 533, 'provider' => 'BetInHell'],
            ['name' => 'Gemstone Of Aztec', 'game_code' => 508, 'provider' => 'BetInHell'],
            ['name' => 'Frozen Rich Joker', 'game_code' => 536, 'provider' => 'BetInHell'],
            ['name' => 'Super Hamster', 'game_code' => 518, 'provider' => 'BetInHell'],
            ['name' => 'Deep Blue Sea', 'game_code' => 519, 'provider' => 'BetInHell'],
            ['name' => 'Horror Castle', 'game_code' => 511, 'provider' => 'BetInHell'],
            ['name' => 'Goblins Land', 'game_code' => 509, 'provider' => 'BetInHell'],

            // IGROSOFT
            ['name' => 'Keks', 'game_code' => 56, 'provider' => 'IGROSOFT'],
            ['name' => 'Crazy Monkey', 'game_code' => 20, 'provider' => 'IGROSOFT'],
            ['name' => 'Island', 'game_code' => 52, 'provider' => 'IGROSOFT'],
            ['name' => 'Zombie', 'game_code' => 103, 'provider' => 'IGROSOFT'],
            ['name' => 'Doughnut', 'game_code' => 27, 'provider' => 'IGROSOFT'],
            ['name' => 'Gnome', 'game_code' => 41, 'provider' => 'IGROSOFT'],
            ['name' => 'Sweet Life', 'game_code' => 93, 'provider' => 'IGROSOFT'],

            // PLAYTECH
            ['name' => 'Azteca', 'game_code' => 4, 'provider' => 'PLAYTECH'],
            ['name' => 'New Year Girls', 'game_code' => 74, 'provider' => 'PLAYTECH'],
            ['name' => 'Nights', 'game_code' => 75, 'provider' => 'PLAYTECH'],
            ['name' => 'Rome & Glory', 'game_code' => 84, 'provider' => 'PLAYTECH'],
            ['name' => 'Viking Striking', 'game_code' => 100, 'provider' => 'PLAYTECH'],
            ['name' => 'Riddle Jungle', 'game_code' => 82, 'provider' => 'PLAYTECH'],
            ['name' => 'Thai Paradise', 'game_code' => 97, 'provider' => 'PLAYTECH'],

            // EROTIC
            ['name' => 'Erotic Fantasy', 'game_code' => 31, 'provider' => 'EROTIC'],
            ['name' => 'Secret Of Temptation', 'game_code' => 118, 'provider' => 'EROTIC'],
            ['name' => 'Secret Of Seduction', 'game_code' => 119, 'provider' => 'EROTIC'],
            ['name' => 'Avatar', 'game_code' => 128, 'provider' => 'EROTIC'],
            ['name' => 'Avengers', 'game_code' => 127, 'provider' => 'EROTIC'],
            ['name' => 'Captain America', 'game_code' => 126, 'provider' => 'EROTIC'],
            ['name' => 'Star Wars', 'game_code' => 132, 'provider' => 'EROTIC'],
        ];

        $providerFilter = $this->option('provider');
        if ($providerFilter) {
            $games = array_filter($games, function($game) use ($providerFilter) {
                return stripos($game['provider'], $providerFilter) !== false;
            });
        }

        $limit = $this->option('limit');
        if ($limit) {
            $games = array_slice($games, 0, (int)$limit);
        }

        $progressBar = $this->output->createProgressBar(count($games));
        $progressBar->start();

        $importedCount = 0;
        $skippedCount = 0;

        foreach ($games as $gameData) {
            $existingGame = SlotegratorGame::where('game_code', $gameData['game_code'])
                ->where('provider_type', 'b2b_slots')
                ->first();

            if ($existingGame) {
                $this->line("Skipping {$gameData['name']} - already exists");
                $skippedCount++;
                $progressBar->advance();
                continue;
            }

            SlotegratorGame::create([
                'uuid' => Str::uuid(),
                'name' => $gameData['name'],
                'game_code' => $gameData['game_code'],
                'provider' => $gameData['provider'],
                'provider_type' => 'b2b_slots',
                'type' => 'slot',
                'technology' => 'html5',
                'has_lobby' => false,
                'is_mobile' => true,
                'has_freespins' => false,
                'has_tables' => false,
                'freespin_valid_until_full_day' => false,
                'is_active' => true,
                'image' => null,
            ]);

            $importedCount++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Import completed!");
        $this->info("📊 Statistics:");
        $this->info("   - New games imported: {$importedCount}");
        $this->info("   - Skipped (already exist): {$skippedCount}");
        $this->info("   - Total processed: " . count($games));

        return self::SUCCESS;
    }
}