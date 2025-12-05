<?php

namespace App\Observers;

use App\Models\Banner;

class BannerObserver extends ImageObserver
{
    public function created(Banner $banner): void
    {
        // Конвертируем основное изображение в WebP
        if ($banner->image) {
            $this->convertToWebP($banner->image);
        }

        // Конвертируем мобильное изображение в WebP
        if ($banner->mobile_image) {
            $this->convertToWebP($banner->mobile_image);
        }
    }

    public function updated(Banner $banner): void
    {
        // Проверяем изменение основного изображения
        if ($banner->isDirty('image')) {
            // Удаляем старый WebP
            if ($banner->getOriginal('image')) {
                $this->deleteWebP($banner->getOriginal('image'));
            }
            
            // Создаем новый WebP
            if ($banner->image) {
                $this->convertToWebP($banner->image);
            }
        }

        // Проверяем изменение мобильного изображения
        if ($banner->isDirty('mobile_image')) {
            // Удаляем старый WebP
            if ($banner->getOriginal('mobile_image')) {
                $this->deleteWebP($banner->getOriginal('mobile_image'));
            }
            
            // Создаем новый WebP
            if ($banner->mobile_image) {
                $this->convertToWebP($banner->mobile_image);
            }
        }
    }

    public function deleting(Banner $banner): void
    {
        // Удаляем WebP файлы при удалении баннера
        if ($banner->image) {
            $this->deleteWebP($banner->image);
        }

        if ($banner->mobile_image) {
            $this->deleteWebP($banner->mobile_image);
        }
    }
}
