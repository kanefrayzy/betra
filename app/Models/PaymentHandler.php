<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentHandler extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'payment_system_id',
        'currency',
        'withdrawal_fee',
        'deposit_fee',
        'url',
        'icon',
        'min_withdrawal_limit',
        'max_withdrawal_limit',
        'min_deposit_limit',
        'max_deposit_limit',
        'active',
        'withdrawal_mode',
        'auto_withdrawal_enabled',
        'daily_auto_withdrawal_limit',
        'require_admin_approval'
    ];

    protected $casts = [
        'active' => 'boolean',
        'auto_withdrawal_enabled' => 'boolean',
        'require_admin_approval' => 'boolean',
        'daily_auto_withdrawal_limit' => 'decimal:2'
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(PaymentSystem::class, 'payment_system_id');
    }

    public function manualPayments(): HasMany
    {
        return $this->hasMany(ManualPayment::class);
    }

    // Scope для фильтрации
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Scope для фильтрации по режиму автовыплат
    public function scopeAutoWithdrawEnabled($query)
    {
        return $query->where('auto_withdrawal_enabled', true);
    }

    public function scopeManualMode($query)
    {
        return $query->where('withdrawal_mode', 'manual');
    }

    public function scopeSemiAutoMode($query)
    {
        return $query->where('withdrawal_mode', 'semi_auto');
    }

    public function scopeInstantMode($query)
    {
        return $query->where('withdrawal_mode', 'instant');
    }

    // Проверка на возможность автовыплаты
    public function isAutoWithdrawalAllowed(): bool
    {
        return $this->active 
            && $this->auto_withdrawal_enabled 
            && in_array($this->withdrawal_mode, ['semi_auto', 'instant']);
    }

    // Проверка на мгновенный режим
    public function isInstantMode(): bool
    {
        return $this->withdrawal_mode === 'instant';
    }

    // Проверка на полуавтоматический режим
    public function isSemiAutoMode(): bool
    {
        return $this->withdrawal_mode === 'semi_auto';
    }

    // Проверка на ручной режим выплат
    public function isManualWithdrawalMode(): bool
    {
        return $this->withdrawal_mode === 'manual';
    }

    // Проверка дневного лимита
    public function checkDailyLimit(float $amount): bool
    {
        if (!$this->daily_auto_withdrawal_limit) {
            return true; // Нет лимита
        }

        $todayTotal = \DB::table('withdrawals')
            ->join('transactions', 'withdrawals.transaction_id', '=', 'transactions.id')
            ->where('transactions.context->payment_handler_id', $this->id)
            ->where('withdrawals.auto_processed', true)
            ->whereDate('withdrawals.created_at', today())
            ->sum('withdrawals.amount');

        return ($todayTotal + $amount) <= $this->daily_auto_withdrawal_limit;
    }
}
