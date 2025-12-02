<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDepositBonus extends Model
{
    protected $fillable = [
        'user_id',
        'deposit_bonus_id',
        'deposit_amount',
        'bonus_amount',
        'wagering_requirement',
        'wagered_amount',
        'completed_at'
    ];

    protected $dates = [
        'completed_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bonus()
    {
        return $this->belongsTo(DepositBonus::class, 'deposit_bonus_id');
    }

    public function isCompleted(): bool
    {
        return $this->wagered_amount >= $this->wagering_requirement;
    }

    public function isFirstDeposit(): bool
    {
        return $this->bonus && in_array($this->bonus->id, [1, 2]);
    }
}
