<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentSystem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'merchant_id',
        'merchant_secret_1',
        'merchant_secret_2',
        'logo',
    ];

    public function handlers(): HasMany
    {
        return $this->hasMany(PaymentHandler::class);
    }
}
