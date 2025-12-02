<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class GameSession extends Model
{
    protected $fillable = [
        'user_id', 'game_id', 'provider', 'token', 'provider_game_token',
        'user_ip', 'device', 'started_at', 'last_activity_at', 'ended_at',
        'session_data'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'ended_at' => 'datetime',
        'session_data' => 'array',
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

    // Методы для работы с сессией
    public function isActive(): bool
    {
        return $this->ended_at === null &&
               $this->last_activity_at &&
               $this->last_activity_at->gt(now()->subMinutes(30));
    }

    public function markActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    public function end(): void
    {
        $this->update(['ended_at' => now()]);
    }

    public function getDuration(): ?int
    {
        if (!$this->started_at) {
            return null;
        }

        $endTime = $this->ended_at ?? $this->last_activity_at ?? now();
        return $this->started_at->diffInSeconds($endTime);
    }

    // Скопы
    public function scopeActive($query)
    {
        return $query->whereNull('ended_at')
                    ->where('last_activity_at', '>', now()->subMinutes(30));
    }

    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
