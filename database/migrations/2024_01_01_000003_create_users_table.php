<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('aes_user_code')->nullable()->index();
            $table->string('username', 30);
            $table->text('avatar');
            $table->string('user_id')->unique();
            $table->string('network_id')->nullable()->unique();
            $table->string('telegram_id')->nullable();
            $table->string('network_type')->nullable();
            $table->string('email')->nullable()->unique();
            $table->foreignId('currency_id')->nullable()->default(3)->constrained('currencies');
            $table->string('password')->nullable();
            $table->double('balance', 20, 2)->default(0.00);
            $table->double('ref_balance')->default(0);
            $table->decimal('rakeback', 10, 6)->default(0.000000);
            $table->string('ip')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_moder')->default(false);
            $table->boolean('is_youtuber')->default(false);
            $table->boolean('is_chat_moder')->default(false);
            $table->boolean('is_withdraw_moder')->nullable()->default(false);
            $table->integer('banchat')->nullable();
            $table->boolean('fake')->default(false);
            $table->boolean('ban')->default(false);
            $table->string('affiliate_id')->nullable();
            $table->string('referred_by')->nullable();
            $table->double('ref_money', 8, 2)->default(0.00);
            $table->double('ref_money_history', 8, 2)->default(0.00);
            $table->decimal('from_ref', 10, 2)->default(0.00);
            $table->string('persona_hash')->nullable();
            $table->text('remember_token')->nullable();
            $table->boolean('need_verify')->default(false);
            $table->string('auth_token', 555)->nullable();
            $table->string('game_token', 555)->nullable();
            $table->decimal('oborot', 10, 3)->default(0.000);
            $table->string('sessionId', 50)->nullable();
            $table->decimal('rain_money', 10, 2)->default(0.00);
            $table->timestamps();
            $table->foreignId('rank_id')->nullable()->default(1)->constrained('ranks');
            $table->timestamp('banned_until')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('payment_ban_at')->nullable();
            $table->boolean('email_changed')->nullable()->default(false);
            $table->decimal('ref_percentage', 5, 2)->nullable();
            $table->boolean('profile_verified')->default(false);
            $table->json('used_bonuses')->nullable();
            $table->decimal('wagering_balance', 10, 2)->default(0.00);
            $table->decimal('wagering_requirement', 15, 2)->default(0.00);
            $table->decimal('wagered_amount', 15, 2)->default(0.00);
            $table->json('active_bonuses')->nullable();
            
            $table->index('balance');
            $table->index('rank_id');
            $table->index('created_at');
            $table->index('aes_user_code');
            $table->index(['username', 'id']);
            $table->index('user_id'); // Индекс для внешнего ключа referred_by
            
            $table->foreign('referred_by')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};