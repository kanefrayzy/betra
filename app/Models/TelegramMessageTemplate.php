<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramMessageTemplate extends Model
{
    protected $fillable = [
        'name',
        'message',
        'category',
        'has_buttons',
        'buttons',
        'is_active',
        'usage_count',
        'last_used_at'
    ];

    protected $casts = [
        'buttons' => 'array',
        'has_buttons' => 'boolean',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime'
    ];

    /**
     * Увеличить счетчик использований
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Получить активные шаблоны
     */
    public static function active()
    {
        return static::where('is_active', true);
    }

    /**
     * Получить шаблоны по категории
     */
    public static function byCategory($category)
    {
        return static::where('category', $category)->where('is_active', true);
    }
}
