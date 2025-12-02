<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_categories', function (Blueprint $table) {
            $table->text('icon')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('game_categories', function (Blueprint $table) {
            $table->string('icon')->nullable()->change();
        });
    }
};
