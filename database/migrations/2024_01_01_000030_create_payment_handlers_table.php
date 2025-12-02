<?php
// 2024_01_01_000030_create_payment_handlers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_handlers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->foreignId('payment_system_id')->constrained('payment_systems');
            $table->string('currency')->default('USD');
            $table->decimal('withdrawal_fee', 5, 2)->nullable();
            $table->decimal('deposit_fee', 5, 2)->nullable();
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->decimal('min_withdrawal_limit', 8, 2)->nullable();
            $table->decimal('max_withdrawal_limit', 8, 2)->nullable();
            $table->decimal('min_deposit_limit', 8, 2)->nullable();
            $table->decimal('max_deposit_limit', 8, 2)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_handlers');
    }
};