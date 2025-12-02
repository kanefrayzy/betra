<?php
// 2024_01_01_000039_create_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('amount', 15, 2);
            $table->foreignId('currency_id')->constrained('currencies');
            $table->string('type');
            $table->string('status')->default('pending');
            $table->json('context')->nullable();
            $table->string('hash')->nullable();
            $table->timestamps();
            
            $table->index('hash');
            $table->index('user_id');
            $table->index(['currency_id', 'type', 'status', 'created_at']);
            $table->index(['currency_id', 'type', 'status', 'amount', 'created_at']);
            $table->index(['type', 'status', 'created_at', 'currency_id', 'amount']);
            $table->index(['currency_id', 'type', 'amount', 'created_at']);
            $table->index('created_at');
            $table->index('amount');
            $table->index(['type', 'status']);
            $table->index(['user_id', 'type', 'status'], 'userid_type_status');
            $table->index(['user_id', 'type', 'status', 'amount', 'created_at'], 'idx_transactions_ref_search');
            $table->index(['user_id', 'created_at'], 'idx_transactions_user_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};