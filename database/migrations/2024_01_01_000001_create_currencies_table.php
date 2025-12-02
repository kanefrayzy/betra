<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index('symbol');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};