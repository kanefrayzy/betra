<?php
// 2024_01_01_000033_create_rate_history_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->decimal('price', 20, 8);
            $table->timestamps();
            
            $table->index(['currency_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_history');
    }
};