<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_crypto_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('currency', 20); // BTC, ETH, USDT, TRX и т.д.
            $table->string('address'); // Адрес кошелька
            $table->string('dest_tag')->nullable(); // Тег назначения (для XRP, XLM и т.д.)
            $table->string('label', 50)->unique(); // Уникальный label для поиска по IPN
            $table->string('network')->nullable(); // TRC20, ERC20, BEP20 и т.д.
            $table->decimal('total_received', 20, 8)->default(0); // Всего получено
            $table->integer('transactions_count')->default(0); // Количество транзакций
            $table->timestamp('last_transaction_at')->nullable(); // Последняя транзакция
            $table->timestamps();

            // Индексы
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'currency', 'network']); // Один адрес на валюту+сеть
            $table->index('address');
            $table->index('label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_crypto_wallets');
    }
};
