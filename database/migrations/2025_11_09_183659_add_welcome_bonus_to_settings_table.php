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
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('welcome_bonus_enabled')->default(false)->after('support_tg');
            $table->decimal('welcome_bonus_amount', 10, 2)->default(0)->after('welcome_bonus_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['welcome_bonus_enabled', 'welcome_bonus_amount']);
        });
    }
};
