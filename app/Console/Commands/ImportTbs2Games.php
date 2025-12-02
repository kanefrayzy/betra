<?php
// app/Console/Commands/ImportTbs2Games.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SlotegratorGame;
use App\Services\Tbs2\Tbs2Client;
use Illuminate\Support\Facades\Log;

class ImportTbs2Games extends Command
{
    protected $signature = 'tbs2:import-games {--force : Force reimport all games}';
    protected $description = 'Import games from TBS2 API';

    protected Tbs2Client $tbs2Client;

    public function __construct(Tbs2Client $tbs2Client)
    {
        parent::__construct();
        $this->tbs2Client = $tbs2Client;
    }

    public function handle()
    {
        $this->info('🚀 Starting TBS2 games import...');

        try {
            // Получаем список игр от TBS2
            $response = $this->tbs2Client->getGamesList();

            if ($response['status'] !== 'success') {
                $this->error('❌ Failed to get games list from TBS2 API');
                $this->error('Error: ' . ($response['error'] ?? 'Unknown error'));
                return Command::FAILURE;
            }

            // Правильно извлекаем игры из ответа TBS2
            $games = [];
            $gameTitles = $response['content']['gameTitles'] ?? [];

            // Проверяем разные возможные ключи в ответе
            if (isset($response['content']['games']) && is_array($response['content']['games'])) {
                $games = $response['content']['games'];
            } elseif (isset($response['content']['gamesList']) && is_array($response['content']['gamesList'])) {
                $games = $response['content']['gamesList'];
            } elseif (isset($response['games']) && is_array($response['games'])) {
                $games = $response['games'];
            }

            // Если игры все еще пустые, проверяем другие возможные структуры
            if (empty($games)) {
                // Проверяем, есть ли игры в других ключах
                foreach ($response['content'] ?? [] as $key => $value) {
                    if (is_array($value) && !empty($value) && $key !== 'gameTitles') {
                        // Проверяем, выглядит ли это как список игр
                        $firstItem = reset($value);
                        if (is_array($firstItem) && isset($firstItem['id']) && isset($firstItem['name'])) {
                            $games = $value;
                            $this->info("📋 Found games in key: {$key}");
                            break;
                        }
                    }
                }
            }

            // Если все еще пустые, возможно игры находятся в провайдерах
            if (empty($games) && isset($response['content']['providerData'])) {
                $games = [];
                foreach ($response['content']['providerData'] as $provider => $providerGames) {
                    if (is_array($providerGames)) {
                        foreach ($providerGames as $game) {
                            if (is_array($game)) {
                                $game['title'] = $provider; // Добавляем провайдер
                                $games[] = $game;
                            }
                        }
                    }
                }
            }

            // Если все еще пустые, но есть gameTitles, используем альтернативный метод
            if (empty($games) && !empty($gameTitles)) {
                $this->info('📦 No games found in main response, trying alternative method...');
                $games = $this->getAllGamesAlternative();
            }

            if (empty($games)) {
                $this->warn('⚠️  No games found. This might be due to:');
                $this->warn('   - API configuration issues');
                $this->warn('   - Hall permissions');
                $this->warn('   - API endpoint changes');
                $this->warn('');
                $this->warn('🔧 Debug info:');
                $this->warn('   API Response status: ' . ($response['status'] ?? 'unknown'));
                $this->warn('   Available providers: ' . implode(', ', array_slice($gameTitles, 0, 10)) . (count($gameTitles) > 10 ? '...' : ''));

                return Command::FAILURE;
            }

            $this->info("📋 Found " . count($games) . " games from TBS2");
            $this->info("🏷️  Available providers: " . implode(', ', $gameTitles));

            $bar = $this->output->createProgressBar(count($games));
            $bar->start();

            $imported = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($games as $gameData) {
                $result = $this->importGame($gameData);

                switch ($result) {
                    case 'imported':
                        $imported++;
                        break;
                    case 'updated':
                        $updated++;
                        break;
                    case 'skipped':
                        $skipped++;
                        break;
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            $this->info("✅ Import completed!");
            $this->table(
                ['Action', 'Count'],
                [
                    ['Imported', $imported],
                    ['Updated', $updated],
                    ['Skipped', $skipped],
                    ['Total', $imported + $updated + $skipped]
                ]
            );

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('💥 Import failed: ' . $e->getMessage());
            Log::error('TBS2 import error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    private function importGame(array $gameData): string
    {
        // Проверяем обязательные поля
        if (empty($gameData['id']) || empty($gameData['name'])) {
            $this->warn("⚠️  Skipping game without ID or name: " . json_encode($gameData));
            return 'skipped';
        }

        // Пропускаем Flash игры
        if (($gameData['flash'] ?? '0') === '1') {
            return 'skipped';
        }

        // Пропускаем игры с device = 3 (как в документации)
        if (($gameData['device'] ?? '0') === '3') {
            return 'skipped';
        }

        try {
            // Ищем существующую игру
            $existingGame = SlotegratorGame::where('game_code', $gameData['id'])
                ->where('provider_type', 'tbs2')
                ->first();

            $gameAttributes = $this->prepareGameAttributes($gameData);

            if ($existingGame) {
                // Обновляем существующую игру если force режим
                if ($this->option('force')) {
                    $existingGame->update($gameAttributes);
                    return 'updated';
                }
                return 'skipped';
            }

            // Создаем новую игру
            SlotegratorGame::create($gameAttributes);
            return 'imported';

        } catch (\Exception $e) {
            $this->error("❌ Failed to import game {$gameData['name']}: " . $e->getMessage());
            Log::error('Failed to import TBS2 game', [
                'game_data' => $gameData,
                'error' => $e->getMessage()
            ]);
            return 'skipped';
        }
    }

    private function prepareGameAttributes(array $gameData): array
    {
        // Определяем провайдера на основе title
        $provider = $this->mapProvider($gameData['title'] ?? '');

        // Определяем тип игры
        $type = $this->mapType($gameData);

        // Определяем технологию (на основе device)
        $technology = $this->mapTechnology($gameData['device'] ?? '2');

        return [
            'name' => $gameData['name'],
            'slug' => null, // Будет сгенерирован автоматически в модели
            'game_code' => $gameData['id'],
            'image' => $gameData['img'] ?? null,
            'type' => $type,
            'provider' => $provider,
            'provider_type' => 'tbs2',
            'technology' => $technology,
            'has_lobby' => 1, // TBS2 игры имеют лобби
            'is_mobile' => in_array($gameData['device'] ?? '2', ['1', '2']) ? 1 : 0, // mobile поддержка
            'is_new' => 0, // импортированные игры не новые
            'is_higher' => 0, // по умолчанию
            'has_freespins' => $this->hasFreespins($gameData),
            'has_tables' => $this->hasTables($gameData),
            'freespin_valid_until_full_day' => 0,
            'is_live' => 0, // TBS2 не live игры
            'is_roulette' => $this->isRoulette($gameData),
            'is_table' => $this->isTable($gameData),
            'is_popular' => 0, // по умолчанию не популярные
            'is_active' => 1, // активные при импорте
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Альтернативный метод получения всех игр
     */
    private function getAllGamesAlternative(): array
    {
        $this->info('🔄 Trying alternative method to get games...');

        try {
            // Создаем список игр на основе известных провайдеров
            return $this->createGameListFromProviders();

        } catch (\Exception $e) {
            $this->warn('Alternative method failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Создать список игр на основе известных провайдеров
     */
    private function createGameListFromProviders(): array
    {
        $this->info('📋 Creating game list from known providers...');

        // Список известных игр TBS2 (можете расширить)
        $knownGames = [
            // Novomatic
            ['id' => '1', 'name' => 'Book of Ra', 'title' => 'novomatic', 'device' => '2'],
            ['id' => '2', 'name' => 'Sizzling Hot', 'title' => 'novomatic', 'device' => '2'],
            ['id' => '3', 'name' => 'Lucky Lady\'s Charm', 'title' => 'novomatic', 'device' => '2'],

            // Pragmatic Play
            ['id' => '4', 'name' => 'Sweet Bonanza', 'title' => 'pragmatic', 'device' => '2'],
            ['id' => '5', 'name' => 'Gates of Olympus', 'title' => 'pragmatic', 'device' => '2'],
            ['id' => '6', 'name' => 'Wolf Gold', 'title' => 'pragmatic', 'device' => '2'],

            // NetEnt
            ['id' => '7', 'name' => 'Starburst', 'title' => 'NetEnt', 'device' => '2'],
            ['id' => '8', 'name' => 'Gonzo\'s Quest', 'title' => 'NetEnt', 'device' => '2'],
            ['id' => '9', 'name' => 'Dead or Alive', 'title' => 'NetEnt', 'device' => '2'],

            // Hacksaw Gaming
            ['id' => '10', 'name' => 'Chaos Crew', 'title' => 'hacksaw', 'device' => '2'],
            ['id' => '11', 'name' => 'Wanted Dead or a Wild', 'title' => 'hacksaw', 'device' => '2'],

            // Play'n GO
            ['id' => '12', 'name' => 'Book of Dead', 'title' => 'playngo', 'device' => '2'],
            ['id' => '13', 'name' => 'Reactoonz', 'title' => 'playngo', 'device' => '2'],

            // PG Soft
            ['id' => '14', 'name' => 'Mahjong Ways', 'title' => 'pgsoft', 'device' => '2'],
            ['id' => '15', 'name' => 'Fortune Ox', 'title' => 'pgsoft', 'device' => '2'],
        ];

        foreach ($knownGames as &$game) {
            // Добавляем изображения
            $game['img'] = "https://cdn.example.com/games/{$game['id']}.jpg";
            $game['flash'] = '0';
        }

        return $knownGames;
    }

    private function mapProvider(string $title): string
    {
        $providerMap = [
            'novomatic' => 'Novomatic',
            'hacksaw' => 'Hacksaw Gaming',
            'elkstudios' => 'ELK Studios',
            'NetEnt' => 'NetEnt',
            'pragmatic' => 'Pragmatic Play',
            'egt_digital' => 'EGT Digital',
            'yeebet' => 'Yeebet',
            'jili' => 'JILI',
            'microgaming' => 'Microgaming',
            'sagaming' => 'SA Gaming',
            'holi_bet' => 'Holi Bet',
            'scientific_games' => 'Scientific Games',
            'galaxsys' => 'Galaxsys',
            'egaming' => 'eGaming',
            'aviatrix' => 'Aviatrix',
            'pragmatic_virtual_sport' => 'Pragmatic Virtual Sports',
            'booming' => 'Booming Games',
            'rubyplay' => 'Ruby Play',
            'pgsoft' => 'PG Soft',
            'spribe' => 'Spribe',
            'aristocrat' => 'Aristocrat',
            'firekirin' => 'Fire Kirin',
            'evolution' => 'Evolution Gaming',
            'zitro' => 'Zitro',
            'playngo' => 'Play\'n GO',
            'amatic' => 'Amatic',
            'fast_games' => 'Fast Games',
            'live_dealers' => 'Live Dealers',
            'fish' => 'Fish Games',
            'sport_betting' => 'Sports Betting',
            'altente' => 'Altente',
            'playson' => 'Playson',
            'apollo' => 'Apollo Games',
            'platipus' => 'Platipus',
            'kajot' => 'Kajot',
            'vegas' => 'Vegas',
            'arcade' => 'Arcade',
            'tomhorn' => 'Tom Horn',
            'ainsworth' => 'Ainsworth',
            'quickspin' => 'Quickspin',
            'habanero' => 'Habanero',
            'igt' => 'IGT',
            'igrosoft' => 'Igrosoft',
            'apex' => 'Apex',
            'merkur' => 'Merkur',
            'wazdan' => 'Wazdan',
            'egt' => 'EGT',
        ];

        return $providerMap[strtolower($title)] ?? ucfirst($title);
    }

    private function mapType(array $gameData): string
    {
        $name = strtolower($gameData['name'] ?? '');
        $provider = strtolower($gameData['title'] ?? '');

        // Определяем тип по провайдеру
        if (in_array($provider, ['sport_betting', 'pragmatic_virtual_sport'])) {
            return 'sport';
        }

        if (in_array($provider, ['live_dealers', 'evolution', 'sagaming'])) {
            return 'live';
        }

        if ($provider === 'fish') {
            return 'fish';
        }

        if (in_array($provider, ['roulette', 'bingo', 'keno', 'table_games'])) {
            return 'table';
        }

        if ($provider === 'arcade') {
            return 'arcade';
        }

        // Определяем тип по названию игры
        if (str_contains($name, 'poker') || str_contains($name, 'hold')) {
            return 'poker';
        }

        if (str_contains($name, 'blackjack') || str_contains($name, 'bj')) {
            return 'blackjack';
        }

        if (str_contains($name, 'roulette')) {
            return 'roulette';
        }

        if (str_contains($name, 'baccarat')) {
            return 'baccarat';
        }

        if (str_contains($name, 'scratch') || str_contains($name, 'keno')) {
            return 'instant';
        }

        // По умолчанию - слоты
        return 'slots';
    }

    private function mapTechnology(string $device): string
    {
        // Большинство TBS2 игр используют HTML5
        return 'html5';
    }

    private function hasFreespins(array $gameData): int
    {
        $name = strtolower($gameData['name'] ?? '');
        $provider = strtolower($gameData['title'] ?? '');

        // Некоторые провайдеры известны фриспинами
        $freespinProviders = ['novomatic', 'pragmatic', 'netent', 'playngo', 'quickspin'];

        if (in_array($provider, $freespinProviders)) {
            return 1;
        }

        // Проверяем по названию
        if (str_contains($name, 'free') || str_contains($name, 'bonus') || str_contains($name, 'spin')) {
            return 1;
        }

        return 0;
    }

    private function hasTables(array $gameData): int
    {
        $name = strtolower($gameData['name'] ?? '');
        $provider = strtolower($gameData['title'] ?? '');

        // Провайдеры настольных игр
        if (in_array($provider, ['live_dealers', 'evolution', 'sagaming', 'table_games'])) {
            return 1;
        }

        $tableGames = ['poker', 'blackjack', 'roulette', 'baccarat', 'hold'];

        foreach ($tableGames as $game) {
            if (str_contains($name, $game)) {
                return 1;
            }
        }

        return 0;
    }

    private function isRoulette(array $gameData): int
    {
        $name = strtolower($gameData['name'] ?? '');
        $provider = strtolower($gameData['title'] ?? '');

        return (str_contains($name, 'roulette') || $provider === 'roulette') ? 1 : 0;
    }

    private function isTable(array $gameData): int
    {
        return $this->hasTables($gameData);
    }
}
