<?php

namespace App\Observers;

use App\Models\SlotegratorGame;

class SlotegratorGameObserver extends ImageObserver
{
    public function updated(SlotegratorGame $game): void
    {
        // Проверяем изменение изображения
        if ($game->isDirty('image')) {
            // Удаляем старый WebP если был локальный файл
            $oldImage = $game->getOriginal('image');
            if ($oldImage && !str_starts_with($oldImage, 'http')) {
                $this->deleteWebP($oldImage);
            }
            
            // Создаем новый WebP если новое изображение локальное
            if ($game->image && !str_starts_with($game->image, 'http')) {
                $this->convertToWebP($game->image);
            }
        }
    }

    public function deleting(SlotegratorGame $game): void
    {
        // Удаляем WebP при удалении игры (только для локальных изображений)
        if ($game->image && !str_starts_with($game->image, 'http')) {
            $this->deleteWebP($game->image);
        }
    }
}
