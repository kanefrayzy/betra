<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyLeaderboard extends Model
{
    use HasFactory;

    protected $table = 'daily_leaderboard';


    protected $fillable = [
        'user_id',
        'daily_oborot',
        'rank',
        'date',
    ];

    protected $dates = ['date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
