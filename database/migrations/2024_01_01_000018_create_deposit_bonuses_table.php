<?php
// 2024_01_01_000018_create_deposit_bonuses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposit_bonuses', function (Blueprint $table) {
            $table->id();
            $table->decimal('required_amount', 10, 2);
            $table->decimal('bonus_amount', 10, 2);
            $table->foreignId('currency_id')->constrained('currencies');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_bonuses');
    }
};