<?php
// 2024_01_01_000013_create_promocode_claims_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promocode_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promocode_id')->constrained('promocodes');
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
            
            $table->index(['user_id', 'promocode_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promocode_claims');
    }
};