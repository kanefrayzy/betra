<?php
// 2024_01_01_000042_create_user_game_histories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_game_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('slotegrator_game_id')->constrained('slotegrator_games');
            $table->string('session_token')->unique();
            $table->string('ip')->nullable();
            $table->string('device')->nullable();
            $table->timestamps();
            
            $table->index('user_id', 'idx_user_game_history_user_id');
            $table->index('slotegrator_game_id', 'idx_user_game_history_slotegrator_game_id');
            $table->index('session_token', 'idx_user_game_history_session_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_game_histories');
    }
};