<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromocodeClaim extends Model
{
    protected $fillable = [
        'promocode_id',
        'user_id',
        'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function promocode()
    {
        return $this->belongsTo(Promocode::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
