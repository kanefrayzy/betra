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
        Schema::table('payment_handlers', function (Blueprint $table) {
            $table->string('network', 50)->nullable()->after('currency')->comment('Сеть для криптовалюты (TRC20, ERC20, BEP20, TON, SOL)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_handlers', function (Blueprint $table) {
            $table->dropColumn('network');
        });
    }
};
