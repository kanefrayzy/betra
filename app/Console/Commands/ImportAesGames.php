<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SlotegratorGame;
use App\Services\Aes\AesClient;
use Illuminate\Support\Facades\Log;

class ImportAesGames extends Command
{
    protected $signature = 'aes:import-games {--force : Force reimport all games}';
    protected $description = 'Import games from AES Gaming API';

    protected AesClient $aesClient;

    public function __construct(AesClient $aesClient)
    {
        parent::__construct();
        $this->aesClient = $aesClient;
    }

    public function handle()
    {
        $this->info('🚀 Starting AES Gaming games import...');

        try {
            // Получаем список провайдеров
            $providersResponse = $this->aesClient->getProviders(1); // English

            if (!$this->aesClient->isSuccess($providersResponse)) {
                $this->error('❌ Failed to get providers list from AES API');
                $this->error('Error: ' . $this->aesClient->getErrorMessage($providersResponse));
                return Command::FAILURE;
            }

            $providers = $providersResponse['data'] ?? [];

            if (empty($providers)) {
                $this->warn('⚠️  No providers found. Check your AES account configuration.');
                return Command::FAILURE;
            }

            $this->info("📋 Found " . count($providers) . " providers from AES Gaming");

            $totalGames = 0;
            $imported = 0;
            $updated = 0;
            $skipped = 0;

            // Проходим по каждому провайдеру и получаем игры
            foreach ($providers as $provider) {
                $providerId = $provider['provider_id'];
                $providerName = $provider['provider_name'] ?? "Provider {$providerId}";
                $providerStatus = $provider['status'] ?? 1;

                // Пропускаем провайдеры на обслуживании
                if ($providerStatus !== 1) {
                    $this->warn("⚠️  Skipping {$providerName} (under maintenance)");
                    continue;
                }

                $this->info("🎮 Fetching games from {$providerName}...");

                try {
                    $gamesResponse = $this->aesClient->getGames($providerId, 1);

                    if (!$this->aesClient->isSuccess($gamesResponse)) {
                        $this->warn("⚠️  Failed to get games for {$providerName}");
                        continue;
                    }

                    $games = $gamesResponse['data'] ?? [];
                    $totalGames += count($games);

                    $this->info("  Found " . count($games) . " games");

                    $bar = $this->output->createProgressBar(count($games));
                    $bar->start();

                    foreach ($games as $gameData) {
                        $result = $this->importGame($gameData, $provider);

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
                    $this->newLine();

                } catch (\Exception $e) {
                    $this->error("❌ Error fetching games for {$providerName}: " . $e->getMessage());
                    continue;
                }
            }

            $this->newLine();
            $this->info("✅ Import completed!");
            $this->table(
                ['Action', 'Count'],
                [
                    ['Providers', count($providers)],
                    ['Total Games Found', $totalGames],
                    ['Imported', $imported],
                    ['Updated', $updated],
                    ['Skipped', $skipped],
                    ['Total Processed', $imported + $updated + $skipped]
                ]
            );

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('💥 Import failed: ' . $e->getMessage());
            Log::error('AES import error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    private function importGame(array $gameData, array $provider): string
    {
        // Проверяем обязательные поля
        if (empty($gameData['game_code']) || empty($gameData['game_name'])) {
            $this->warn("⚠️  Skipping game without code or name: " . json_encode($gameData));
            return 'skipped';
        }

        // Пропускаем игры, которые нельзя запустить
        if (!($gameData['launch_enable'] ?? true)) {
            return 'skipped';
        }

        try {
            $providerId = $provider['provider_id'];
            $providerName = $provider['provider_name'] ?? "Provider {$providerId}";

            // Создаем уникальный game_code с информацией о провайдере
            $uniqueGameCode = json_encode([
                'provider_id' => $providerId,
                'game_symbol' => $gameData['game_code']
            ]);

            // Ищем существующую игру
            $existingGame = SlotegratorGame::where('game_code', $uniqueGameCode)
                ->where('provider_type', 'aes')
                ->first();

            $gameAttributes = $this->prepareGameAttributes($gameData, $provider);

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
            $this->error("❌ Failed to import game {$gameData['game_name']}: " . $e->getMessage());
            Log::error('Failed to import AES game', [
                'game_data' => $gameData,
                'error' => $e->getMessage()
            ]);
            return 'skipped';
        }
    }

    private function prepareGameAttributes(array $gameData, array $provider): array
    {
        $providerId = $provider['provider_id'];
        $providerName = $provider['provider_name'] ?? "Provider {$providerId}";

        // Определяем тип игры
        $category = $gameData['category'] ?? 'Slots';
        $type = $this->mapType($category);

        // game_code как JSON с provider_id и game_symbol
        $gameCode = json_encode([
            'provider_id' => $providerId,
            'game_symbol' => $gameData['game_code']
        ]);

        return [
            'name' => $gameData['game_name'],
            'slug' => null, // Будет сгенерирован автоматически в модели
            'game_code' => $gameCode,
            'image' => $gameData['game_image'] ?? null,
            'type' => $type,
            'provider' => $providerName,
            'provider_type' => 'aes',
            'technology' => 'html5',
            'has_lobby' => 1,
            'is_mobile' => 1,
            'is_new' => 0,
            'is_higher' => 0,
            'has_freespins' => $this->hasFreespins($gameData),
            'has_tables' => $this->hasTables($type),
            'freespin_valid_until_full_day' => 0,
            'is_live' => $type === 'live' ? 1 : 0,
            'is_roulette' => $this->isRoulette($gameData),
            'is_table' => $this->isTable($type),
            'is_popular' => 0,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function mapType(string $category): string
    {
        $typeMap = [
            'Slots' => 'slots',
            'Live' => 'live',
            'Table' => 'table',
            'Casino' => 'casino',
        ];

        return $typeMap[$category] ?? 'slots';
    }

    private function hasFreespins(array $gameData): int
    {
        $gameName = strtolower($gameData['game_name'] ?? '');

        // Проверяем по названию
        if (str_contains($gameName, 'free') ||
            str_contains($gameName, 'bonus') ||
            str_contains($gameName, 'spin')) {
            return 1;
        }

        return 0;
    }

    private function hasTables(string $type): int
    {
        return in_array($type, ['live', 'table', 'poker', 'blackjack', 'roulette', 'baccarat']) ? 1 : 0;
    }

    private function isRoulette(array $gameData): int
    {
        $gameName = strtolower($gameData['game_name'] ?? '');
        return str_contains($gameName, 'roulette') ? 1 : 0;
    }

    private function isTable(string $type): int
    {
        return $this->hasTables($type);
    }
}
