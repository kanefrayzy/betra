<?php
// 2024_01_01_000037_create_tournaments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('prize_pool', 20, 2);
            $table->json('prize_distribution')->nullable();
            $table->timestamp('start_date')->useCurrent();
            $table->timestamp('end_date')->default('0000-00-00 00:00:00');
            $table->string('status')->default('upcoming');
            $table->decimal('min_turnover', 20, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};