<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralBonus extends Model
{
    protected $fillable = [
        'referrer_user_id',
        'referral_user_id',
        'amount',
    ];
}
