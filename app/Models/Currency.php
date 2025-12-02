<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'symbol',
        'active',
        'icon',
        'sort_order',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function rate(): HasOne
    {
        return $this->hasOne(Rate::class);
    }

    /**
     * Скоуп для активных валют
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Скоуп для сортировки
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
