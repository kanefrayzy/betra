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
        Schema::table('payment_handlers', function (Blueprint $table) {
            // Режим обработки выплат: manual (ручной), semi_auto (полуавтомат - требует подтверждения), instant (мгновенный)
            $table->enum('withdrawal_mode', ['manual', 'semi_auto', 'instant'])
                  ->default('manual')
                  ->after('active')
                  ->comment('Режим обработки выплат');
            
            // Включена ли автоматическая обработка выплат для этого handler
            $table->boolean('auto_withdrawal_enabled')
                  ->default(false)
                  ->after('withdrawal_mode')
                  ->comment('Включена ли автовыплата');
            
            // Дневной лимит для автоматических выплат
            $table->decimal('daily_auto_withdrawal_limit', 10, 2)
                  ->nullable()
                  ->after('auto_withdrawal_enabled')
                  ->comment('Дневной лимит автовыплат');
            
            // Требуется ли подтверждение администратора для полуавтоматического режима
            $table->boolean('require_admin_approval')
                  ->default(true)
                  ->after('daily_auto_withdrawal_limit')
                  ->comment('Требуется ли одобрение админа (для semi_auto)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_handlers', function (Blueprint $table) {
            $table->dropColumn([
                'withdrawal_mode',
                'auto_withdrawal_enabled',
                'daily_auto_withdrawal_limit',
                'require_admin_approval'
            ]);
        });
    }
};
