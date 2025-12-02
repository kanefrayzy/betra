<?php
// 2024_01_01_000011_create_promocode_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promocode', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->integer('limit');
            $table->integer('amount')->nullable();
            $table->integer('count_use')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promocode');
    }
};