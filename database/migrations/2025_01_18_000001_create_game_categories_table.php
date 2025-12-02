<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // SVG icon or class name
            $table->string('color')->default('#ffb300'); // Category color
            $table->integer('order')->default(0); // Sort order
            $table->boolean('is_active')->default(true);
            $table->boolean('show_on_homepage')->default(true);
            $table->timestamps();

            $table->index('order');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_categories');
    }
};
