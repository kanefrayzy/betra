<?php
// 2024_01_01_000014_create_rain_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rain', function (Blueprint $table) {
            $table->id();
            $table->integer('rain_id');
            $table->integer('rainer_id');
            $table->integer('accept_id');
            $table->decimal('allsum', 10, 2);
            $table->integer('count');
            $table->decimal('realsum', 10, 2);
            $table->integer('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rain');
    }
};