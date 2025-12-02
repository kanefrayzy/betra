<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlinkoGame extends Model
{
    protected $fillable = [
        'user_id',
        'bet_amount',
        'risk_level',
        'rows',
        'positions',
        'win_amount',
        'multiplier',
        'status',
        'hash',
        'server_seed',
        'client_seed',
    ];

    protected $casts = [
        'positions' => 'array',
        'bet_amount' => 'decimal:8',
        'win_amount' => 'decimal:8',
        'multiplier' => 'decimal:2'
    ];

    // Константы для множителей
    const MULTIPLIERS = [
        'low' => [1000, 130, 26, 9, 4, 2, 0.2, 0.2, 0.2, 0.2, 0.2, 2, 4, 9, 26, 130, 1000],
        'medium' => [1000, 130, 26, 9, 4, 2, 0.2, 0.2, 0.2, 0.2, 0.2, 2, 4, 9, 26, 130, 1000],
        'high' => [1000, 130, 26, 9, 4, 2, 0.2, 0.2, 0.2, 0.2, 0.2, 2, 4, 9, 26, 130, 1000]
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getMultipliers($riskLevel)
    {
        return self::MULTIPLIERS[$riskLevel] ?? self::MULTIPLIERS['medium'];
    }
}
