<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ImageOptimizationService
{
    public static function convertToWebP(string $path): ?string
    {
        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
            return null;
        }

        $pathInfo = pathinfo($path);
        $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
        
        if (file_exists(public_path($webpPath))) {
            return $webpPath;
        }

        try {
            $img = Image::make(public_path($path));
            $img->encode('webp', 85)->save(public_path($webpPath));
            
            return $webpPath;
        } catch (\Exception $e) {
            \Log::error('WebP conversion failed: ' . $e->getMessage());
            return null;
        }
    }

    public static function optimizeImage(string $path, int $quality = 85): bool
    {
        try {
            $img = Image::make(public_path($path));
            
            if ($img->width() > 1920) {
                $img->resize(1920, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            $img->save(public_path($path), $quality);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Image optimization failed: ' . $e->getMessage());
            return false;
        }
    }

    public static function getWebPPath(string $originalPath): string
    {
        $webpPath = self::convertToWebP($originalPath);
        return $webpPath ?? $originalPath;
    }
}
