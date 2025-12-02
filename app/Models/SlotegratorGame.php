<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

/**
 * @property mixed|string $name
 * @property string $provider_type
 */
class SlotegratorGame extends Model
{
    use Searchable;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'image',
        'type',
        'provider',
        'provider_type',
        'game_code',
        'technology',
        'has_lobby',
        'is_mobile',
        'has_freespins',
        'has_tables',
        'freespin_valid_until_full_day',
        'is_active',
        'is_live',
        'is_new',
        'is_popular',
        'is_higher',
        'is_table',
        'is_roulette',
    ];

    protected $casts = [
        'has_lobby' => 'boolean',
        'is_mobile' => 'boolean',
        'has_freespins' => 'boolean',
        'has_tables' => 'boolean',
        'freespins_valid_until_full_day' => 'boolean',
        'is_active' => 'boolean',
        'is_live' => 'boolean',
        'is_new' => 'boolean',
        'is_popular' => 'boolean',
        'is_higher' => 'boolean',
        'is_table' => 'boolean',
        'is_roulette' => 'boolean',
    ];

    /**
     * Автоматически генерируем slug при создании/обновлении
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($game) {
            if (empty($game->slug)) {
                $game->slug = $game->generateUniqueSlug();
            }
        });

        static::updating(function ($game) {
            if (empty($game->slug) || $game->isDirty(['name', 'provider_type'])) {
                $game->slug = $game->generateUniqueSlug();
            }
        });
    }

    /**
     * Генерируем уникальный slug
     */
    public function generateUniqueSlug(): string
    {
        $baseSlug = $this->createBaseSlug();
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Создаем базовый slug из названия и провайдера
     */
    public function createBaseSlug(): string
    {
        $gameName = $this->sanitizeGameName($this->name);
        $providerName = $this->sanitizeGameName($this->provider); // Используем provider, а не provider_type

        return $gameName . '-' . $providerName;
    }


    /**
     * Очищаем название для slug (работает и для игры, и для провайдера)
     */
    public function sanitizeGameName(string $name): string
    {
        // Сохраняем важные символы как разделители
        $name = str_replace(['™', '®', '©'], '', $name);
        $name = str_replace(['&', '+', 'and'], '-and-', $name);
        $name = str_replace(['vs', 'vs.', 'v.s.'], '-vs-', $name);

        // Заменяем специальные символы на дефисы
        $name = preg_replace('/[^\w\s\-]/', '-', $name);

        // Заменяем пробелы на дефисы
        $name = preg_replace('/\s+/', '-', $name);

        // Убираем множественные дефисы
        $name = preg_replace('/-+/', '-', $name);

        // Убираем дефисы в начале и конце
        $name = trim($name, '-');

        // Приводим к нижнему регистру
        $name = strtolower($name);

        // Ограничиваем длину slug
        if (strlen($name) > 50) {
            $name = substr($name, 0, 50);
            $name = rtrim($name, '-');
        }

        return $name;
    }

    /**
     * Получаем короткое название провайдера для slug (убрано, теперь не используется)
     */
    public function getProviderSlug(): string
    {
        // Этот метод больше не используется, но оставляем для совместимости
        return $this->sanitizeGameName($this->provider);
    }

    /**
     * Поиск игры по slug
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->where('is_active', 1)->first();
    }

    /**
     * Получаем URL для игры
     */
    public function getPlayUrlAttribute(): string
    {
        return route('game.play', $this->slug);
    }

    /**
     * Получаем URL для демо
     */
    public function getDemoUrlAttribute(): string
    {
        return route('game.demo', $this->slug);
    }

    public function searchableAs(): string
    {
        return 'slotegrator_games_index';
    }

    public function toSearchableArray(): array
    {
        return [
            'name' => (string)$this->name,
        ];
    }

    public function favoriteByUsers(): HasMany
    {
        return $this->hasMany(FavoriteGame::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(GameLike::class);
    }

    public function gameHistories(): HasMany
    {
        return $this->hasMany(UserGameHistory::class, 'slotegrator_game_id');
    }

    /**
     * Категории этой игры
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            GameCategory::class,
            'category_game',
            'slotegrator_game_id',
            'game_category_id'
        )
        ->withPivot('order')
        ->withTimestamps();
    }

    /**
     * Scope для активных игр
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для live игр
     */
    public function scopeLive(Builder $query): Builder
    {
        return $query->where('is_live', true);
    }

    /**
     * Scope для новых игр
     */
    public function scopeNew(Builder $query): Builder
    {
        return $query->where('is_new', true);
    }

    /**
     * Scope для популярных игр
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->where('is_popular', true);
    }

    /**
     * Scope для игр по провайдеру
     */
    public function scopeByProvider(Builder $query, string $provider): Builder
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope для игр по типу провайдера
     */
    public function scopeByProviderType(Builder $query, string $providerType): Builder
    {
        return $query->where('provider_type', $providerType);
    }
}