<?php
// 2024_01_01_000007_create_slotegrator_games_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slotegrator_games', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable()->unique();
            $table->string('game_code')->nullable();
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->string('image')->nullable();
            $table->string('type')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_type')->default('slotegrator');
            $table->string('technology')->nullable();
            $table->boolean('has_lobby')->default(false);
            $table->boolean('is_mobile')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_higher')->default(false);
            $table->boolean('has_freespins')->default(false);
            $table->boolean('has_tables')->default(false);
            $table->unsignedInteger('freespin_valid_until_full_day')->default(0);
            $table->timestamps();
            $table->boolean('is_live')->default(false);
            $table->tinyInteger('is_roulette')->default(0);
            $table->tinyInteger('is_table')->default(0);
            $table->tinyInteger('is_popular')->default(0);
            $table->boolean('is_active')->default(true);
            
            $table->index('uuid');
            $table->index('name');
            $table->index('provider');
            $table->index('provider_type');
            $table->index('game_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slotegrator_games');
    }
};