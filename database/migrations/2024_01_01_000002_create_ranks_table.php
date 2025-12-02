<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('picture')->nullable();
            $table->decimal('oborot_min', 15, 2);
            $table->decimal('oborot_max', 15, 2);
            $table->decimal('rakeback', 8, 4)->nullable();
            $table->decimal('daily_min', 8, 2)->nullable();
            $table->decimal('daily_max', 10, 2)->nullable();
            $table->decimal('percent', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ranks');
    }
};