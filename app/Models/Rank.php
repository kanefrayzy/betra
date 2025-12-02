<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'picture',
        'oborot_min',
        'oborot_max',
        'rakeback',
        'percent',
        'daily_min',
        'daily_max',
    ];


}
