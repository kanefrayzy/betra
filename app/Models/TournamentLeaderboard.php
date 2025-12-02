<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentLeaderboard extends Model
{
    protected $table = 'tournament_leaderboard';

    protected $fillable = [
        'tournament_id',
        'user_id',
        'turnover',
        'position',
        'prize'
    ];

    protected $casts = [
        'turnover' => 'decimal:2',
        'prize' => 'decimal:2'
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
