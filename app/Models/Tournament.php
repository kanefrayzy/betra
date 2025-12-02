<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $fillable = [
        'name',
        'description',
        'prize_pool',
        'prize_distribution',
        'start_date',
        'end_date',
        'status',
        'min_turnover'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'prize_distribution' => 'array',
        'prize_pool' => 'decimal:2',
        'min_turnover' => 'decimal:2'
    ];

    public function leaderboard()
    {
        return $this->hasMany(TournamentLeaderboard::class);
    }
}
