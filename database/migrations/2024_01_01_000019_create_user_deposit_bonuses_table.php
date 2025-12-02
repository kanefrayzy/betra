<?php
// 2024_01_01_000019_create_user_deposit_bonuses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_deposit_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('deposit_bonus_id')->constrained('deposit_bonuses');
            $table->decimal('deposit_amount', 10, 2);
            $table->decimal('bonus_amount', 10, 2);
            $table->decimal('wagering_requirement', 10, 2);
            $table->decimal('wagered_amount', 10, 2)->default(0.00);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'deposit_bonus_id']);
            $table->index(['user_id', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_deposit_bonuses');
    }
};