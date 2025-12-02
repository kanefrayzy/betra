<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{

    protected $fillable = [
        'user_id',
        'details',
        'amount',
        'transaction_id',
        'external_id',
        'status',
        'processed_at',
        'comment',
        'betatransfer_transaction_id',
        'betatransfer_status',
        'auto_processed',
        'admin_approved_by',
        'admin_approved_at',
    ];


    protected $casts = [
        'status' => PaymentStatus::class,
        'processed_at' => 'datetime',
        'auto_processed' => 'boolean',
        'admin_approved_at' => 'datetime',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function adminApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    // Scope для автоматически обработанных выплат
    public function scopeAutoProcessed($query)
    {
        return $query->where('auto_processed', true);
    }

    // Scope для ручных выплат
    public function scopeManualProcessed($query)
    {
        return $query->where('auto_processed', false);
    }

    // Проверка на автоматическую обработку
    public function isAutoProcessed(): bool
    {
        return $this->auto_processed;
    }

    // Проверка на одобрение админом
    public function isAdminApproved(): bool
    {
        return !is_null($this->admin_approved_by) && !is_null($this->admin_approved_at);
    }

    // Проверка на наличие BetaTransfer транзакции
    public function hasBetaTransferTransaction(): bool
    {
        return !is_null($this->betatransfer_transaction_id);
    }
}
