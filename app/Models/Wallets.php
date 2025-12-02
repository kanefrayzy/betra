<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallets extends Model
{

    protected $fillable = [
        'wallet',
        'system'
    ];

    protected $hidden = ['created_at', 'updated_at'];

}
