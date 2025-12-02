<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class B2BSlotsGame extends Model
{
    protected $fillable = [
        'game_code', 'game_group', 'api_settings',
        'supported_currencies', 'supported_languages',
        'min_bet', 'max_bet', 'rtp_percentage'
    ];

    protected $casts = [
        'api_settings' => 'array',
        'supported_currencies' => 'array',
        'supported_languages' => 'array',
        'min_bet' => 'decimal:5',
        'max_bet' => 'decimal:5',
    ];

    // Полиморфная связь с основной таблицей игр
    public function game(): MorphOne
    {
        return $this->morphOne(Game::class, 'providable');
    }

    // Методы для работы с настройками API
    public function getLines(): array
    {
        return $this->api_settings['lines'] ?? [];
    }

    public function getBets(): array
    {
        return $this->api_settings['bets'] ?? [];
    }

    public function getDenominations(): array
    {
        return $this->api_settings['denominations'] ?? [];
    }

    public function isHardBets(): bool
    {
        return $this->api_settings['hardBets'] ?? false;
    }

    public function isCurrencySupported(string $currency): bool
    {
        return in_array(strtoupper($currency), $this->supported_currencies ?? []);
    }

    public function isLanguageSupported(string $language): bool
    {
        return in_array(strtolower($language), $this->supported_languages ?? []);
    }

    // Скопы
    public function scopeByGameCode($query, int $gameCode)
    {
        return $query->where('game_code', $gameCode);
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('game_group', $group);
    }
}
