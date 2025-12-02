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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('total_games')->default(0)->after('oborot')->comment('Общее количество игр');
            $table->integer('total_wins')->default(0)->after('total_games')->comment('Общее количество выигрышей');
            $table->decimal('total_bets_amount', 20, 2)->default(0.00)->after('total_wins')->comment('Общая сумма ставок в USD');
            $table->decimal('total_wins_amount', 20, 2)->default(0.00)->after('total_bets_amount')->comment('Общая сумма выигрышей в USD');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['total_games', 'total_wins', 'total_bets_amount', 'total_wins_amount']);
        });
    }
};
