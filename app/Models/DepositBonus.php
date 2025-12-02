<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositBonus extends Model
{
    protected $fillable = [
        'required_amount',
        'bonus_amount',
        'currency_id'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function userBonuses()
    {
        return $this->hasMany(UserDepositBonus::class);
    }
}
