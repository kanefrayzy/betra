<?php

namespace App\Console\Commands;

use App\Models\SlotegratorGame;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportB2bGamesCommand extends Command
{
    protected $signature = 'games:import-b2b';
    protected $description = 'Import B2B Slots games from predefined list';

    protected $games = [
        // NETENT (97 games)
        ['name' => 'Piggy Riches', 'game_code' => 1001, 'provider' => 'NETENT'],
        ['name' => 'Stickers', 'game_code' => 1002, 'provider' => 'NETENT'],
        ['name' => 'Fruit Shop', 'game_code' => 1003, 'provider' => 'NETENT'],
        ['name' => 'Flowers', 'game_code' => 1005, 'provider' => 'NETENT'],
        ['name' => 'Aloha', 'game_code' => 1054, 'provider' => 'NETENT'],
        ['name' => 'Red Riding Hood', 'game_code' => 1069, 'provider' => 'NETENT'],
        ['name' => 'Hansel & Gretel', 'game_code' => 1068, 'provider' => 'NETENT'],

        // NOVOMATIC DELUXE (87 games)
        ['name' => 'Just jewels', 'game_code' => 54, 'provider' => 'NOVOMATIC DELUXE'],
        ['name' => 'Fruit farm', 'game_code' => 34, 'provider' => 'NOVOMATIC DELUXE'],
        ['name' => 'Helena', 'game_code' => 47, 'provider' => 'NOVOMATIC DELUXE'],
        ['name' => 'Sea sirens', 'game_code' => 86, 'provider' => 'NOVOMATIC DELUXE'],
        ['name' => 'Sizzling hot deluxe', 'game_code' => 92, 'provider' => 'NOVOMATIC DELUXE'],
        ['name' => 'Cryptic highway', 'game_code' => 21, 'provider' => 'NOVOMATIC DELUXE'],
        ['name' => 'Just jewels deluxe', 'game_code' => 55, 'provider' => 'NOVOMATIC DELUXE'],

        // PlaynGO (92 games)
        ['name' => 'Jewel Box', 'game_code' => 3000, 'provider' => 'PlaynGO'],
        ['name' => 'Lady Of Fortune', 'game_code' => 3001, 'provider' => 'PlaynGO'],
        ['name' => 'Samba Carnival', 'game_code' => 3002, 'provider' => 'PlaynGO'],
        ['name' => 'Merry Xmas', 'game_code' => 3003, 'provider' => 'PlaynGO'],
        ['name' => 'Book Of Dead', 'game_code' => 3004, 'provider' => 'PlaynGO'],
        ['name' => 'Doom of Egypt', 'game_code' => 3005, 'provider' => 'PlaynGO'],
        ['name' => 'Rise Of Merlin', 'game_code' => 3006, 'provider' => 'PlaynGO'],

        // YGGDRASIL (28 games)
        ['name' => 'Rainbow Ryan', 'game_code' => 2001, 'provider' => 'YGGDRASIL'],
        ['name' => 'Sunny Shores', 'game_code' => 2014, 'provider' => 'YGGDRASIL'],
        ['name' => 'Vikings Go Berzerk', 'game_code' => 2024, 'provider' => 'YGGDRASIL'],
        ['name' => 'Double Dragons', 'game_code' => 2023, 'provider' => 'YGGDRASIL'],
        ['name' => 'Gem Rocks', 'game_code' => 2022, 'provider' => 'YGGDRASIL'],
        ['name' => 'Easter Island', 'game_code' => 2021, 'provider' => 'YGGDRASIL'],
        ['name' => 'Pumpkin Smash', 'game_code' => 2019, 'provider' => 'YGGDRASIL'],

        // Pragmatic Play (183 games)
        ['name' => 'Book Of Tut', 'game_code' => 4000, 'provider' => 'Pragmatic Play'],
        ['name' => 'Book of Vikings', 'game_code' => 4001, 'provider' => 'Pragmatic Play'],
        ['name' => 'Return of the Dead', 'game_code' => 4002, 'provider' => 'Pragmatic Play'],
        ['name' => 'Scarab Queen', 'game_code' => 4003, 'provider' => 'Pragmatic Play'],
        ['name' => 'Heart of Rio', 'game_code' => 4004, 'provider' => 'Pragmatic Play'],
        ['name' => 'Madame Destiny', 'game_code' => 4005, 'provider' => 'Pragmatic Play'],
        ['name' => 'Ancient Egypt Classic', 'game_code' => 4006, 'provider' => 'Pragmatic Play'],

        // PUSH GAMING (26 games)
        ['name' => 'Hearts Highway', 'game_code' => 5000, 'provider' => 'PUSH GAMING'],
        ['name' => 'Fat Rabbit', 'game_code' => 5001, 'provider' => 'PUSH GAMING'],
        ['name' => 'Fat Santa', 'game_code' => 5002, 'provider' => 'PUSH GAMING'],
        ['name' => '10 Swords', 'game_code' => 5003, 'provider' => 'PUSH GAMING'],
        ['name' => 'Fire Hopper', 'game_code' => 5005, 'provider' => 'PUSH GAMING'],
        ['name' => 'Blaze Of Ra', 'game_code' => 5006, 'provider' => 'PUSH GAMING'],
        ['name' => 'Razor Shark', 'game_code' => 5007, 'provider' => 'PUSH GAMING'],

        // BetInHell (33 games)
        ['name' => 'Book Of Sunrise', 'game_code' => 533, 'provider' => 'BetInHell'],
        ['name' => 'Gemstone Of Aztec', 'game_code' => 508, 'provider' => 'BetInHell'],
        ['name' => 'Frozen Rich Joker', 'game_code' => 536, 'provider' => 'BetInHell'],
        ['name' => 'Super Hamster', 'game_code' => 518, 'provider' => 'BetInHell'],
        ['name' => 'Deep Blue Sea', 'game_code' => 519, 'provider' => 'BetInHell'],
        ['name' => 'Horror Castle', 'game_code' => 511, 'provider' => 'BetInHell'],
        ['name' => 'Goblins Land', 'game_code' => 509, 'provider' => 'BetInHell'],

        // IGROSOFT (18 games)
        ['name' => 'Keks', 'game_code' => 56, 'provider' => 'IGROSOFT'],
        ['name' => 'Zombie', 'game_code' => 103, 'provider' => 'IGROSOFT'],
        ['name' => 'Doughnut', 'game_code' => 27, 'provider' => 'IGROSOFT'],
        ['name' => 'BetInHell', 'game_code' => 10, 'provider' => 'IGROSOFT'],
        ['name' => 'Island', 'game_code' => 52, 'provider' => 'IGROSOFT'],
        ['name' => 'Gnome', 'game_code' => 41, 'provider' => 'IGROSOFT'],
        ['name' => 'Sweet Life', 'game_code' => 93, 'provider' => 'IGROSOFT'],

        // PLAYTECH (14 games)
        ['name' => 'Azteca', 'game_code' => 4, 'provider' => 'PLAYTECH'],
        ['name' => 'New Year Girls', 'game_code' => 74, 'provider' => 'PLAYTECH'],
        ['name' => 'Nights', 'game_code' => 75, 'provider' => 'PLAYTECH'],
        ['name' => 'Rome & Glory', 'game_code' => 84, 'provider' => 'PLAYTECH'],
        ['name' => 'Viking Striking', 'game_code' => 100, 'provider' => 'PLAYTECH'],
        ['name' => 'Riddle Jungle', 'game_code' => 82, 'provider' => 'PLAYTECH'],
        ['name' => 'Thai Paradise', 'game_code' => 97, 'provider' => 'PLAYTECH'],

        // EROTIC (12 games)
        ['name' => 'Erotic Fantasy', 'game_code' => 31, 'provider' => 'EROTIC'],
        ['name' => 'Secret Of Temptation', 'game_code' => 118, 'provider' => 'EROTIC'],
        ['name' => 'Secret Of Seduction', 'game_code' => 119, 'provider' => 'EROTIC'],
        ['name' => 'Avatar', 'game_code' => 128, 'provider' => 'EROTIC'],
        ['name' => 'Avengers', 'game_code' => 127, 'provider' => 'EROTIC'],
        ['name' => 'Captain America', 'game_code' => 126, 'provider' => 'EROTIC'],
        ['name' => 'Star Wars', 'game_code' => 132, 'provider' => 'EROTIC'],

        // И так далее... (здесь показаны только первые игры каждого провайдера)
    ];

    public function handle()
    {
        $this->info('Starting B2B Slots games import...');

        $imported = 0;
        $skipped = 0;

        foreach ($this->games as $gameData) {
            // Проверяем, существует ли уже игра с таким кодом
            $existingGame = SlotegratorGame::where('game_code', $gameData['game_code'])
                ->where('provider_type', 'b2b_slots')
                ->first();

            if ($existingGame) {
                $this->line("Skipping {$gameData['name']} - already exists");
                $skipped++;
                continue;
            }

            // Создаем новую игру
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
                'image' => null, // Можно добавить позже
            ]);

            $this->line("Imported: {$gameData['name']} (Code: {$gameData['game_code']})");
            $imported++;
        }

        $this->info("Import completed!");
        $this->info("Imported: {$imported} games");
        $this->info("Skipped: {$skipped} games");

        return 0;
    }
}
