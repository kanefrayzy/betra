<?php
// 2024_01_01_000009_create_bonus_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonus', function (Blueprint $table) {
            $table->id();
            $table->decimal('sum', 10, 2)->default(0.00);
            $table->string('color')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonus');
    }
};