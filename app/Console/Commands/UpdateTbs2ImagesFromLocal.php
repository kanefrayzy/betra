<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SlotegratorGame;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class UpdateTbs2ImagesFromLocal extends Command
{
    protected $signature = 'tbs2:update-images-local';
    protected $description = 'Update TBS2 game images from local folder';

    public function handle()
    {
        $imagesPath = public_path('assets/images/slots');

        if (!File::exists($imagesPath)) {
            $this->error("❌ Folder not found: {$imagesPath}");
            return Command::FAILURE;
        }

        $images = File::files($imagesPath);
        $this->info("📁 Found " . count($images) . " images in folder");

        $games = SlotegratorGame::where('provider_type', 'b2b')->get();
        $this->info("🎮 Found " . $games->count() . " TBS2 games in DB");

        $bar = $this->output->createProgressBar($games->count());
        $bar->start();

        $updated = 0;
        $notFound = 0;

        foreach ($games as $game) {
            $slug = Str::slug($game->name);
            $found = false;

            // Ищем по разным вариантам
            $variants = [
                "{$slug}.jpg",
                "{$slug}.png",
                "{$slug}.webp",
                str_replace('-', '_', $slug) . ".jpg",
                strtolower(str_replace(' ', '-', $game->name)) . ".jpg",
            ];

            foreach ($variants as $variant) {
                $imagePath = "{$imagesPath}/{$variant}";

                if (File::exists($imagePath)) {
                    $game->update([
                        'image' => "/assets/images/slots/{$variant}"
                    ]);
                    $updated++;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $notFound++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Updated: {$updated}");
        $this->warn("❌ Not found: {$notFound}");

        return Command::SUCCESS;
    }
}
