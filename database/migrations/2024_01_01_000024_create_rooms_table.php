<?php
// 2024_01_01_000024_create_rooms_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('min', 10, 2)->default(1.00);
            $table->decimal('max', 10, 2)->default(1000.00);
            $table->integer('time')->default(30);
            $table->integer('bets')->default(10);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};