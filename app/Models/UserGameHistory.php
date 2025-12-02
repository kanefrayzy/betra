<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGameHistory extends Model
{
    protected $fillable = [
        'user_id', 'slotegrator_game_id', 'game_id', 'provider',
        'session_token', 'ip', 'device'
    ];

    // Связи
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    // Старая связь для совместимости
    public function slotegratorGame(): BelongsTo
    {
        return $this->belongsTo(SlotegratorGame::class, 'slotegrator_game_id');
    }

    // Скопы
    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
