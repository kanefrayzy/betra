<?php
// 2024_01_01_000040_create_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('details')->nullable();
            $table->foreignId('transaction_id')->constrained('transactions');
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->foreignId('currency_id')->constrained('currencies');
            $table->string('status')->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->text('comment')->nullable();
            $table->string('external_id')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'processed_at', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};