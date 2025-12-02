<?php
// 2024_01_01_000016_create_daily_bonus_tokens_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_bonus_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->date('date')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_bonus_tokens');
    }
};