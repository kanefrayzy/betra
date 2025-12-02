<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rain extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'rain_id',
        'rainer_id',
        'accept_id',
        'allsum',
        'count',
        'realsum',
        'date',
    ];

}
