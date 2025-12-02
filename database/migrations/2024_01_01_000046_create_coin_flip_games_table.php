<?php
// 2024_01_01_000046_create_coin_flip_games_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coin_flip_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('currency')->default('AZN');
            $table->integer('bet');
            $table->string('choice');
            $table->string('result');
            $table->boolean('is_winner');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coin_flip_games');
    }
};