<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{

    protected $fillable = [
        'sum',
        'color',
        'status'
    ];

    protected $hidden = ['created_at', 'updated_at'];

}
