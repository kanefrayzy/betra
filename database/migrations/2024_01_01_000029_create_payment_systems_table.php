<?php
// 2024_01_01_000029_create_payment_systems_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_systems', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('merchant_id');
            $table->string('merchant_secret_1');
            $table->string('merchant_secret_2')->nullable();
            $table->boolean('active')->default(false);
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_systems');
    }
};