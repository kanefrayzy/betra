<?php
// 2024_01_01_000025_create_jackpot_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jackpot', function (Blueprint $table) {
            $table->id();
            $table->integer('game_id');
            $table->string('room');
            $table->foreignId('winner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('winner_chance', 5, 2)->nullable();
            $table->integer('winner_ticket')->nullable();
            $table->decimal('winner_sum', 10, 2)->nullable();
            $table->string('hash');
            $table->decimal('price', 10, 2)->default(0.00);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
            
            $table->unique(['room', 'game_id']);
            $table->index(['room', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jackpot');
    }
};