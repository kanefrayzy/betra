<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'is_rain', 
        'isMoneyTransfer',
        'is_winning_share',
        'room',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
