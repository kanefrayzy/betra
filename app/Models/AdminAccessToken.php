<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAccessToken extends Model
{
    protected $fillable = ['token', 'expires_at', 'pin'];

    protected $dates = ['expires_at'];

    public function isValid()
    {
        return $this->expires_at->isFuture();
    }
}
