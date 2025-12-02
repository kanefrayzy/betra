<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Scout\Searchable;
use App\Services\GameProviderFactory;

class Game extends Model
{
    use Searchable;

    protected $fillable = [
        'name', 'image', 'type', 'provider', 'technology',
        'providable_type', 'providable_id',
        'has_lobby', 'is_mobile', 'is_new', 'is_higher',
        'has_freespins', 'has_tables', 'is_live', 'is_roulette',
        'is_table', 'is_popular', 'is_active'
    ];

    protected $casts = [
        'has_lobby' => 'boolean',
        'is_mobile' => 'boolean',
        'is_new' => 'boolean',
        'is_higher' => 'boolean',
        'has_freespins' => 'boolean',
        'has_tables' => 'boolean',
        'is_live' => 'boolean',
        'is_roulette' => 'boolean',
        'is_table' => 'boolean',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Полиморфная связь с конкретным провайдером
    public function providable(): MorphTo
    {
        return $this->morphTo();
    }

    // Связи с другими таблицами
    public function favoriteByUsers(): HasMany
    {
        return $this->hasMany(FavoriteGame::class);
    }

    public function gameHistories(): HasMany
    {
        return $this->hasMany(UserGameHistory::class);
    }

    public function gameSessions(): HasMany
    {
        return $this->hasMany(GameSession::class);
    }

    // Скопы для фильтрации
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeMobile($query)
    {
        return $query->where('is_mobile', true);
    }

    public function scopeWithFreespins($query)
    {
        return $query->where('has_freespins', true);
    }

    // Методы для работы с провайдером
    public function getProviderInstance()
    {
        return GameProviderFactory::create($this->provider);
    }

    public function canPlay(User $user): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $provider = $this->getProviderInstance();
        return $provider->canUserPlay($user, $this);
    }

    // Поиск
    public function searchableAs(): string
    {
        return 'games_index';
    }

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'provider' => $this->provider,
            'type' => $this->type,
        ];
    }

    // Аксессоры
    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('images/games/default.jpg');
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return asset($this->image);
    }
}
