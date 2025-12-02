<?php
// 2024_01_01_000045_create_admin_tasks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained('admin_users');
            $table->foreignId('assignee_id')->constrained('admin_users');
            $table->text('description');
            $table->string('screenshot')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_tasks');
    }
};