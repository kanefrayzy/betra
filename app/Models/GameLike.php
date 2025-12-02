<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slotegrator_game_id',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function game(): BelongsTo
    {
        return $this->belongsTo(SlotegratorGame::class);
    }
}
