<?php
// app/Console/Commands/GenerateGameSlugs.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SlotegratorGame;

class GenerateGameSlugs extends Command
{
    protected $signature = 'games:generate-slugs {--force : Regenerate existing slugs}';
    protected $description = 'Generate slugs for existing games';

    public function handle()
    {
        $this->info('🚀 Generating slugs for games...');

        $query = SlotegratorGame::query();

        if (!$this->option('force')) {
            $query->whereNull('slug');
        }

        $games = $query->get();

        if ($games->isEmpty()) {
            $this->info('✅ No games need slug generation');
            return Command::SUCCESS;
        }

        $this->info("📋 Processing {$games->count()} games...");

        // Сначала проверяем потенциальные конфликты
        $this->checkPotentialConflicts($games);

        $bar = $this->output->createProgressBar($games->count());
        $bar->start();

        $generated = 0;
        $updated = 0;
        $errors = 0;
        $conflicts = [];

        foreach ($games as $game) {
            try {
                $oldSlug = $game->slug;
                $newSlug = $game->generateUniqueSlug();

                // Проверяем на конфликт
                $existing = SlotegratorGame::where('slug', $newSlug)
                    ->where('id', '!=', $game->id)
                    ->first();

                if ($existing) {
                    $conflicts[] = [
                        'slug' => $newSlug,
                        'game1' => "{$game->name} (ID: {$game->id})",
                        'game2' => "{$existing->name} (ID: {$existing->id})"
                    ];
                    // Принудительно генерируем уникальный slug
                    $newSlug = $newSlug . '-' . $game->id;
                }

                $game->slug = $newSlug;
                $game->save();

                if ($oldSlug === null) {
                    $generated++;
                } else {
                    $updated++;
                }

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("❌ Failed to generate slug for game {$game->name}: " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Slug generation completed!");
        $this->table(
            ['Result', 'Count'],
            [
                ['Generated', $generated],
                ['Updated', $updated],
                ['Errors', $errors],
                ['Conflicts resolved', count($conflicts)],
                ['Total processed', $games->count()]
            ]
        );

        if (!empty($conflicts)) {
            $this->warn('⚠️  Resolved conflicts:');
            $this->table(
                ['Slug', 'Game 1', 'Game 2'],
                $conflicts
            );
        }

        return Command::SUCCESS;
    }

    private function checkPotentialConflicts($games)
    {
        $this->info('🔍 Checking for potential conflicts...');

        $slugCounts = [];
        foreach ($games as $game) {
            $baseSlug = $this->generateBaseSlug($game);
            $slugCounts[$baseSlug] = ($slugCounts[$baseSlug] ?? 0) + 1;
        }

        $conflicts = array_filter($slugCounts, fn($count) => $count > 1);

        if (!empty($conflicts)) {
            $this->warn('⚠️  Found potential conflicts:');
            foreach ($conflicts as $slug => $count) {
                $this->line("   - {$slug}: {$count} games");
            }
            $this->info('🔧 Will resolve automatically by adding game IDs');
            $this->newLine();
        }
    }

    private function generateBaseSlug($game): string
    {
        // Создаем временный экземпляр для генерации базового slug
        $tempGame = new SlotegratorGame();
        $tempGame->name = $game->name;
        $tempGame->provider = $game->provider; // Используем provider вместо provider_type

        return $tempGame->createBaseSlug();
    }
}
