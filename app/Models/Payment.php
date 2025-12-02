<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'details',
        'amount',
        'currency_id',
        'transaction_id',
        'external_id',
        'status',
        'processed_at',
        'comment',
    ];


    protected $casts = [
        'status' => PaymentStatus::class,
        'processed_at' => 'datetime',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
