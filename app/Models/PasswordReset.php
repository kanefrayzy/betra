<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'email', 'token', 'expires_at'];

    protected $dates = ['expires_at'];

    public $timestamps = false;
}
