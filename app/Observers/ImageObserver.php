<?php

namespace App\Observers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageObserver
{
    /**
     * Автоматическая конвертация изображения в WebP после сохранения
     */
    public function convertToWebP(string $imagePath, int $quality = 75): ?string
    {
        if (!file_exists(public_path($imagePath))) {
            return null;
        }

        $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        
        // Пропускаем если уже WebP или не поддерживаемый формат
        if ($extension === 'webp' || !in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return null;
        }

        try {
            $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $imagePath);
            
            $manager = new ImageManager(new Driver());
            $img = $manager->read(public_path($imagePath));
            $img->toWebp($quality)->save(public_path($webpPath));
            
            \Log::info("WebP created: {$webpPath}");
            
            return $webpPath;
        } catch (\Exception $e) {
            \Log::error("WebP conversion failed for {$imagePath}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Удаление WebP файла при удалении оригинала
     */
    public function deleteWebP(string $imagePath): void
    {
        if (empty($imagePath)) {
            return;
        }

        $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $imagePath);
        
        if (file_exists(public_path($webpPath))) {
            @unlink(public_path($webpPath));
            \Log::info("WebP deleted: {$webpPath}");
        }
    }
}
