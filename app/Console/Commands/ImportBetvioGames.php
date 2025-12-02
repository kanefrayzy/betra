<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SlotegratorGame;
use App\Services\Betvio\BetvioClient;
use Illuminate\Support\Facades\Log;

class ImportBetvioGames extends Command
{
    protected $signature = 'betvio:import-games {--currency= : Currency code (USD, EUR, etc)} {--force : Force reimport all games}';
    protected $description = 'Import games from Betvio Gaming API';

    protected BetvioClient $betvioClient;

    public function __construct(BetvioClient $betvioClient)
    {
        parent::__construct();
        $this->betvioClient = $betvioClient;
    }

    public function handle()
    {
        $this->info('🚀 Starting Betvio Gaming games import...');

        try {
            // Определяем валюту для импорта
            $currency = $this->option('currency');
            if (!$currency) {
                // Импортируем для первой доступной валюты
                $currencies = BetvioClient::getConfiguredCurrencies();
                if (empty($currencies)) {
                    $this->error('❌ No Betvio accounts configured');
                    return Command::FAILURE;
                }
                $currency = $currencies[0];
            }

            $this->info("💰 Using currency: {$currency}");
            $this->betvioClient = new BetvioClient($currency);

            // Получаем список провайдеров для слотов
            $this->info('📋 Fetching providers...');
            $providersResponse = $this->betvioClient->getProviders('slot');

            if (!$this->betvioClient->isSuccess($providersResponse)) {
                $this->error('❌ Failed to get providers list from Betvio API');
                $this->error('Error: ' . $this->betvioClient->getErrorMessage($providersResponse));
                return Command::FAILURE;
            }

            $providers = $providersResponse['providers'] ?? [];

            if (empty($providers)) {
                $this->warn('⚠️  No providers found. Check your Betvio account configuration.');
                return Command::FAILURE;
            }

            $this->info("📋 Found " . count($providers) . " providers from Betvio Gaming");

            $totalGames = 0;
            $imported = 0;
            $updated = 0;
            $skipped = 0;

            // Проходим по каждому провайдеру и получаем игры
            foreach ($providers as $provider) {
                $providerCode = $provider['code'];
                $providerName = $provider['name'] ?? $providerCode;

                $this->info("🎮 Fetching games from {$providerName} ({$providerCode})...");

                try {
                    // Получаем игры провайдера
                    $gamesResponse = $this->betvioClient->getGames($providerCode, 'en');

                    if (!$this->betvioClient->isSuccess($gamesResponse)) {
                        $this->warn("⚠️  Failed to get games for {$providerName}");
                        continue;
                    }

                    $games = $gamesResponse['games'] ?? [];
                    $availableGames = array_filter($games, fn($game) => ($game['status'] ?? 0) === 1);
                    
                    $totalGames += count($availableGames);
                    $this->info("  Found " . count($availableGames) . " available games");

                    if (empty($availableGames)) {
                        continue;
                    }

                    $bar = $this->output->createProgressBar(count($availableGames));
                    $bar->start();

                    foreach ($availableGames as $gameData) {
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
            Log::error('Betvio import error', [
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

        // Пропускаем недоступные игры
        if (($gameData['status'] ?? 0) !== 1) {
            return 'skipped';
        }

        try {
            $providerCode = $provider['code'];
            $providerName = $provider['name'] ?? $providerCode;

            // Создаем уникальный game_code с информацией о провайдере
            $uniqueGameCode = json_encode([
                'provider_code' => $providerCode,
                'game_code' => $gameData['game_code']
            ]);

            // Ищем существующую игру
            $existingGame = SlotegratorGame::where('game_code', $uniqueGameCode)
                ->where('provider_type', 'betvio')
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
            Log::error('Failed to import Betvio game', [
                'game_data' => $gameData,
                'error' => $e->getMessage()
            ]);
            return 'skipped';
        }
    }

    private function prepareGameAttributes(array $gameData, array $provider): array
    {
        $providerCode = $provider['code'];
        $providerName = $provider['name'] ?? $providerCode;

        // game_code как JSON с provider_code и game_code
        $gameCode = json_encode([
            'provider_code' => $providerCode,
            'game_code' => $gameData['game_code']
        ]);

        return [
            'name' => $gameData['game_name'],
            'slug' => null, // Будет сгенерирован автоматически в модели
            'game_code' => $gameCode,
            'image' => $gameData['banner'] ?? null,
            'type' => 'slots', // Betvio в основном слоты
            'provider' => $providerName,
            'provider_type' => 'betvio',
            'technology' => 'html5',
            'has_lobby' => 1,
            'is_mobile' => 1,
            'is_new' => 0,
            'is_higher' => 0,
            'has_freespins' => $this->hasFreespins($gameData),
            'has_tables' => 0,
            'freespin_valid_until_full_day' => 0,
            'is_live' => 0,
            'is_roulette' => $this->isRoulette($gameData),
            'is_table' => 0,
            'is_popular' => 0,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
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

    private function isRoulette(array $gameData): int
    {
        $gameName = strtolower($gameData['game_name'] ?? '');
        return str_contains($gameName, 'roulette') ? 1 : 0;
    }
}
