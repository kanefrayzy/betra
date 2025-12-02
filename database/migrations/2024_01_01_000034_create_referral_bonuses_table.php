<?php
// 2024_01_01_000034_create_referral_bonuses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_bonuses', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('referrer_user_id')->constrained('users');
            $table->foreignId('referral_user_id')->constrained('users');
            $table->decimal('amount', 20, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_bonuses');
    }
};