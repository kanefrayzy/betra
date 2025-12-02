<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    protected $fillable = [
        'code',
        'amount_type',
        'amount',
        'min_amount',
        'max_amount',
        'usage_limit',
        'used_count',
        'is_active'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'usage_limit' => 'integer',
        'used_count' => 'integer'
    ];

    // Связь с историей активаций
    public function claims()
    {
        return $this->hasMany(PromocodeClaim::class);
    }

    // Проверка доступности промокода
    public function isAvailable()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    // Проверка использования пользователем
    public function isUsedByUser($userId)
    {
        return $this->claims()->where('user_id', $userId)->exists();
    }
}
