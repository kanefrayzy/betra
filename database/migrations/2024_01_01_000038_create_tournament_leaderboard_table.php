<?php
// 2024_01_01_000038_create_tournament_leaderboard_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_leaderboard', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments');
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('turnover', 20, 2)->default(0.00);
            $table->integer('position')->default(0);
            $table->decimal('prize', 20, 2)->default(0.00);
            $table->timestamps();
            
            $table->unique(['tournament_id', 'user_id'], 'tournament_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_leaderboard');
    }
};