<?php
// 2024_01_01_000031_create_plinko_games_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plinko_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('bet_amount', 12, 2);
            $table->decimal('win_amount', 12, 2)->default(0.00);
            $table->decimal('multiplier', 12, 2)->default(0.00);
            $table->enum('risk_level', ['low', 'medium', 'high']);
            $table->integer('rows');
            $table->json('positions');
            $table->string('hash');
            $table->string('server_seed');
            $table->string('client_seed');
            $table->string('transaction_id')->nullable();
            $table->string('win_transaction_id')->nullable();
            $table->enum('status', ['created', 'completed'])->default('created');
            $table->timestamps();
            
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plinko_games');
    }
};