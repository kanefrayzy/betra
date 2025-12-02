<?php
// 2024_01_01_000027_create_messages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('username')->nullable();
            $table->foreignId('parent_id')->nullable();
            $table->boolean('is_rain')->default(false);
            $table->text('message');
            $table->timestamps();
            $table->boolean('isMoneyTransfer')->default(false);
            $table->boolean('is_winning_share')->default(false);
            $table->unsignedBigInteger('winning_id')->nullable();
            $table->string('room')->default('global');
            
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};