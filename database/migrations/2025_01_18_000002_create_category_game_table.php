<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_game', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_category_id')->constrained('game_categories')->onDelete('cascade');
            $table->foreignId('slotegrator_game_id')->constrained('slotegrator_games')->onDelete('cascade');
            $table->integer('order')->default(0); // Order within category
            $table->timestamps();

            $table->unique(['game_category_id', 'slotegrator_game_id']);
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_game');
    }
};
