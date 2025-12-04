<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'mobile_image',
        'link',
        'type',
        'locale',
        'order',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Скоупы для фильтрации
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
    }

    public function scopeForLocale(Builder $query, string $locale): Builder
    {
        return $query->where('locale', $locale);
    }

    public function scopeMainSlider(Builder $query): Builder
    {
        return $query->where('type', 'main');
    }

    public function scopeSmallBanners(Builder $query): Builder
    {
        return $query->where('type', 'small');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('created_at');
    }

    // Получить активные баннеры для главного слайдера
    public static function getMainSliderBanners(string $locale = null): \Illuminate\Database\Eloquent\Collection
    {
        $locale = $locale ?: app()->getLocale();
        
        return \Cache::remember("main_banners_{$locale}", 3600, function () use ($locale) {
            return self::active()
                ->mainSlider()
                ->forLocale($locale)
                ->ordered()
                ->get();
        });
    }

    // Получить активные маленькие баннеры
    public static function getSmallBanners(string $locale = null): \Illuminate\Database\Eloquent\Collection
    {
        $locale = $locale ?: app()->getLocale();
        
        return \Cache::remember("small_banners_{$locale}", 3600, function () use ($locale) {
            return self::active()
                ->smallBanners()
                ->forLocale($locale)
                ->ordered()
                ->get();
        });
    }

    /**
     * Очистить кеш баннеров
     */
    public static function clearCache(): void
    {
        $locales = ['ru', 'en', 'az', 'uz', 'kz', 'tr'];
        
        foreach ($locales as $locale) {
            \Cache::forget("main_banners_{$locale}");
            \Cache::forget("small_banners_{$locale}");
        }
    }

    /**
     * Boot метод для автоматической очистки кеша
     */
    protected static function boot()
    {
        parent::boot();
        
        // Очищаем кеш при создании, обновлении или удалении баннера
        static::saved(function () {
            self::clearCache();
        });
        
        static::deleted(function () {
            self::clearCache();
        });
    }

    // Получить путь к изображению с проверкой существования
    public function getImageUrlAttribute(): string
    {
        if ($this->image && file_exists(public_path($this->image))) {
            return asset($this->image);
        }
        
        // Fallback изображение
        return asset('assets/images/banners/default-banner.png');
    }

    // Получить путь к мобильному изображению с проверкой существования
    public function getMobileImageUrlAttribute(): string
    {
        $mobileImage = $this->mobile_image ?: $this->image;
        
        if ($mobileImage && file_exists(public_path($mobileImage))) {
            return asset($mobileImage);
        }
        
        // Fallback изображение
        return asset('assets/images/banners/default-banner.png');
    }
}
