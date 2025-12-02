<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    protected $fillable = ['username', 'password', 'is_super_admin'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_super_admin' => 'boolean',
    ];
}
