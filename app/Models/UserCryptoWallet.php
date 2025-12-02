<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCryptoWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency',
        'address',
        'dest_tag',
        'label',
        'network',
        'total_received',
        'transactions_count',
        'last_transaction_at',
    ];

    protected $casts = [
        'total_received' => 'decimal:8',
        'last_transaction_at' => 'datetime',
    ];

    /**
     * Владелец кошелька
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Генерирует уникальный label для пользователя
     */
    public static function generateLabel(int $userId, string $currency, ?string $network = null): string
    {
        $suffix = $network ? "_{$network}" : '';
        return "user_{$userId}_{$currency}{$suffix}_" . time();
    }

    /**
     * Форматирование адреса для отображения
     */
    public function getFormattedAddressAttribute(): string
    {
        if (strlen($this->address) <= 12) {
            return $this->address;
        }
        
        return substr($this->address, 0, 8) . '...' . substr($this->address, -8);
    }

    /**
     * Проверка, требуется ли dest_tag для данной валюты
     */
    public static function requiresDestTag(string $currency): bool
    {
        return in_array(strtoupper($currency), ['XRP', 'XLM', 'EOS']);
    }

    /**
     * Увеличение счетчика транзакций и суммы
     */
    public function incrementStats(float $amount): void
    {
        $this->increment('transactions_count');
        $this->increment('total_received', $amount);
        $this->update(['last_transaction_at' => now()]);
    }
}
