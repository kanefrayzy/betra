<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ConvertImagesToWebP extends Command
{
    protected $signature = 'images:convert-webp 
                            {path? : Path to images directory}
                            {--quality=85 : WebP quality (0-100)}';

    protected $description = 'Convert all images to WebP format';

    public function handle()
    {
        $path = $this->argument('path') ?? public_path('assets/images/slots');
        $quality = (int) $this->option('quality');

        if (!is_dir($path)) {
            $this->error("Directory not found: {$path}");
            return 1;
        }

        $this->info("Converting images in: {$path}");
        $this->info("Quality: {$quality}");
        $this->newLine();

        $manager = new ImageManager(new Driver());
        $extensions = ['jpg', 'jpeg', 'png'];
        $files = File::allFiles($path);
        
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $converted = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());
            
            if (!in_array($extension, $extensions)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file->getPathname());
            
            if (file_exists($webpPath)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            try {
                $img = $manager->read($file->getPathname());
                $img->toWebp($quality)->save($webpPath);
                $converted++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Failed: {$file->getFilename()} - {$e->getMessage()}");
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Status', 'Count'],
            [
                ['Converted', $converted],
                ['Skipped', $skipped],
                ['Errors', $errors],
                ['Total', count($files)]
            ]
        );

        $this->info('Done!');
        return 0;
    }
}
