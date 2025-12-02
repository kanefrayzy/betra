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
        Schema::table('withdrawals', function (Blueprint $table) {
            // ID транзакции в системе BetaTransfer
            $table->string('betatransfer_transaction_id', 100)
                  ->nullable()
                  ->after('external_id')
                  ->comment('ID транзакции в BetaTransfer');
            
            // Статус транзакции в BetaTransfer (pending, success, failed, etc.)
            $table->string('betatransfer_status', 50)
                  ->nullable()
                  ->after('betatransfer_transaction_id')
                  ->comment('Статус в BetaTransfer');
            
            // Была ли выплата обработана автоматически
            $table->boolean('auto_processed')
                  ->default(false)
                  ->after('betatransfer_status')
                  ->comment('Обработана автоматически');
            
            // ID администратора, одобрившего выплату (для semi_auto режима)
            $table->unsignedBigInteger('admin_approved_by')
                  ->nullable()
                  ->after('accepted_by')
                  ->comment('ID админа, одобрившего выплату');
            
            // Время одобрения администратором
            $table->timestamp('admin_approved_at')
                  ->nullable()
                  ->after('admin_approved_by')
                  ->comment('Время одобрения админом');
            
            // Добавим индексы для быстрого поиска
            $table->index('betatransfer_transaction_id');
            $table->index(['auto_processed', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropIndex(['betatransfer_transaction_id']);
            $table->dropIndex(['auto_processed', 'status']);
            
            $table->dropColumn([
                'betatransfer_transaction_id',
                'betatransfer_status',
                'auto_processed',
                'admin_approved_by',
                'admin_approved_at'
            ]);
        });
    }
};
