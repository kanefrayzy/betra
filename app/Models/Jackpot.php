<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jackpot extends Model
{
    public const STATUS_NOT_STARTED = 0;
    public const STATUS_PLAYING = 1;
    public const STATUS_PRE_FINISH = 2;
    public const STATUS_FINISHED = 3;

    protected $table = 'jackpot';

    protected $fillable = [
        'game_id',
        'room',
        'winner_id',
        'winner_chance',
        'winner_ticket',
        'winner_sum',
        'hash',
        'price',
        'status'
    ];

    protected $casts = [
        'game_id' => 'integer',
        'winner_id' => 'integer',
        'winner_chance' => 'float',
        'winner_ticket' => 'integer',
        'winner_sum' => 'decimal:2',
        'price' => 'decimal:2',
        'status' => 'integer'
    ];

    public static function getBank(string $room): float
    {
        return static::where('room', $room)
            ->latest('id')
            ->value('price') ?? 0;
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function bets(): HasMany
    {
        return $this->hasMany(JackpotBet::class, 'game_id', 'game_id');
    }

    public function participants()
    {
        return $this->bets()
            ->join('users', 'jackpot_bets.user_id', '=', 'users.id')
            ->select('users.*')
            ->groupBy('users.id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room', 'name');
    }
}
