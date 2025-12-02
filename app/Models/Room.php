<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $table = 'rooms';

    protected $fillable = [
        'name',
        'min',
        'max',
        'time',
        'bets',
        'status'
    ];

    protected $casts = [
        'min' => 'float',
        'max' => 'float',
        'time' => 'integer',
        'bets' => 'integer',
        'status' => 'boolean'
    ];

    public function games()
    {
        return $this->hasMany(Jackpot::class, 'room', 'name');
    }
}
