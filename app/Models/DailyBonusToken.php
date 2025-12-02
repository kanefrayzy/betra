<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyBonusToken extends Model
{
    protected $fillable = ['token', 'date'];

    protected $casts = [
        'date' => 'date',
    ];
}
