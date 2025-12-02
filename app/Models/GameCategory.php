<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class GameCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'order',
        'is_active',
        'show_on_homepage',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_on_homepage' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Автоматически генерируем slug при создании/обновлении
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Игры в этой категории
     */
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(
            SlotegratorGame::class,
            'category_game',
            'game_category_id',
            'slotegrator_game_id'
        )
        ->withPivot('order')
        ->withTimestamps()
        ->orderBy('category_game.order');
    }

    /**
     * Активные игры в этой категории
     */
    public function activeGames(): BelongsToMany
    {
        return $this->games()->where('slotegrator_games.is_active', true);
    }

    /**
     * Получить категории для главной страницы
     */
    public static function forHomepage()
    {
        return static::where('is_active', true)
            ->where('show_on_homepage', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Получить все активные категории
     */
    public static function active()
    {
        return static::where('is_active', true)
            ->orderBy('order')
            ->get();
    }
}
