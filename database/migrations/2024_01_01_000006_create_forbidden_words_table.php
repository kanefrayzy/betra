<?php
// 2024_01_01_000006_create_forbidden_words_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forbidden_words', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->string('type');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forbidden_words');
    }
};