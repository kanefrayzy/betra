<?php
// 2024_01_01_000022_create_game_likes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('slotegrator_game_id')->constrained('slotegrator_games');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_likes');
    }
};