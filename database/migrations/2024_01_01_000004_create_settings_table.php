<?php
// 2024_01_01_000004_create_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->nullable();
            $table->string('sitename')->nullable();
            $table->string('title')->nullable();
            $table->text('desc')->nullable();
            $table->text('keys')->nullable();
            $table->double('min_rain_amount', 10, 2)->nullable();
            $table->double('max_rain_amount', 10, 2)->nullable();
            $table->integer('min_rain_count')->nullable();
            $table->integer('max_rain_count')->nullable();
            $table->double('min_rain_user_oborot', 12, 4)->nullable();
            $table->string('chat_mess_support')->nullable();
            $table->integer('chat_status')->nullable();
            $table->string('ip_maintenance')->nullable();
            $table->string('text_maintenance')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->decimal('withdrawal_commission', 5, 2)->default(0.00);
            $table->string('support_tg', 55)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};