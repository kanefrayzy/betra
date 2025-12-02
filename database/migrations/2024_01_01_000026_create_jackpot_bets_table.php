<?php
// 2024_01_01_000026_create_jackpot_bets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jackpot_bets', function (Blueprint $table) {
            $table->id();
            $table->string('room');
            $table->integer('game_id');
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('sum', 10, 2);
            $table->integer('from');
            $table->integer('to');
            $table->string('color', 6);
            $table->boolean('is_fake')->default(false);
            $table->timestamps();
            
            $table->index(['room', 'game_id']);
            $table->index(['user_id', 'game_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jackpot_bets');
    }
};