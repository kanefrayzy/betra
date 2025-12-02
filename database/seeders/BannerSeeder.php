<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        // Главные баннеры на русском
        Banner::create([
            'title' => 'FRENZY FEST',
            'description' => 'Один спин — и ты в игре!',
            'image' => 'banners/main-banner-1-ru.jpg', // путь к изображению
            'type' => 'main',
            'locale' => 'ru',
            'order' => 1,
            'is_active' => true,
        ]);

        Banner::create([
            'title' => 'ЕЖЕДНЕВНЫЙ БОНУС',
            'description' => '+5% к депозиту каждый день',
            'image' => 'banners/main-banner-2-ru.jpg',
            'type' => 'main',
            'locale' => 'ru',
            'order' => 2,
            'is_active' => true,
        ]);

        // Маленькие баннеры на русском
        Banner::create([
            'title' => 'Игра недели',
            'description' => 'Новые призы каждую неделю',
            'image' => 'banners/small-banner-1-ru.jpg',
            'type' => 'small',
            'locale' => 'ru',
            'order' => 1,
            'is_active' => true,
        ]);

        Banner::create([
            'title' => 'Кешбэк',
            'description' => 'Возврат до 10%',
            'image' => 'banners/small-banner-2-ru.jpg',
            'type' => 'small',
            'locale' => 'ru',
            'order' => 2,
            'is_active' => true,
        ]);

        // Главные баннеры на английском
        Banner::create([
            'title' => 'FRENZY FEST',
            'description' => 'One spin and you are in the game!',
            'image' => 'banners/main-banner-1-en.jpg',
            'type' => 'main',
            'locale' => 'en',
            'order' => 1,
            'is_active' => true,
        ]);

        Banner::create([
            'title' => 'DAILY BONUS',
            'description' => '+5% deposit bonus every day',
            'image' => 'banners/main-banner-2-en.jpg',
            'type' => 'main',
            'locale' => 'en',
            'order' => 2,
            'is_active' => true,
        ]);

        // Маленькие баннеры на английском
        Banner::create([
            'title' => 'Game of the Week',
            'description' => 'New prizes every week',
            'image' => 'banners/small-banner-1-en.jpg',
            'type' => 'small',
            'locale' => 'en',
            'order' => 1,
            'is_active' => true,
        ]);

        Banner::create([
            'title' => 'Cashback',
            'description' => 'Up to 10% return',
            'image' => 'banners/small-banner-2-en.jpg',
            'type' => 'small',
            'locale' => 'en',
            'order' => 2,
            'is_active' => true,
        ]);

        // Баннеры на казахском
        Banner::create([
            'title' => 'FRENZY FEST',
            'description' => 'Бір айналдыру — және сіз ойында!',
            'image' => 'banners/main-banner-1-kz.jpg',
            'type' => 'main',
            'locale' => 'kz',
            'order' => 1,
            'is_active' => true,
        ]);

        Banner::create([
            'title' => 'КҮНДЕЛІКТІ БОНУС',
            'description' => 'Күн сайын +5% депозитке',
            'image' => 'banners/main-banner-2-kz.jpg',
            'type' => 'main',
            'locale' => 'kz',
            'order' => 2,
            'is_active' => true,
        ]);
    }
}
