<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('telegram_message_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название шаблона
            $table->text('message'); // Текст сообщения
            $table->string('category')->nullable(); // Категория (акции, уведомления и т.д.)
            $table->boolean('has_buttons')->default(false); // Есть ли кнопки
            $table->json('buttons')->nullable(); // JSON с кнопками
            $table->boolean('is_active')->default(true); // Активен ли шаблон
            $table->integer('usage_count')->default(0); // Счетчик использований
            $table->timestamp('last_used_at')->nullable(); // Когда последний раз использовался
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_message_templates');
    }
};
