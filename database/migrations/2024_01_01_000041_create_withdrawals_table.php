<?php
// 2024_01_01_000041_create_withdrawals_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('details')->nullable();
            $table->foreignId('transaction_id')->constrained('transactions');
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->string('status')->default('pending');
            $table->string('pay_system', 55)->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('comment')->nullable();
            $table->string('external_id')->nullable();
            $table->integer('accepted_by')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'processed_at', 'external_id']);
            $table->index(['status', 'updated_at'], 'idx_status_date');
            $table->index('details', 'idx_details');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};