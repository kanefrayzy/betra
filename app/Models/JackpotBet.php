<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JackpotBet extends Model
{
    protected $fillable = [
        'room',
        'game_id',
        'user_id',
        'sum',
        'from',
        'to',
        'color',
        'is_fake'
    ];

    protected $casts = [
        'game_id' => 'integer',
        'sum' => 'decimal:2',
        'from' => 'integer',
        'to' => 'integer',
        'is_fake' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Jackpot::class, 'game_id', 'game_id')
            ->where('room', $this->room);
    }
}
