<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinFlip extends Model
{
    use HasFactory;

    protected $table = 'coin_flip_games';

    protected $fillable = [
        'user_id',
        'currency',
        'bet',
        'choice',
        'result',
        'is_winner',
    ];

    /**
     * Получить пользователя, которому принадлежит ставка.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
