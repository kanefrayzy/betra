<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVerification extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'birth_date',
        'document_type',
        'document_front',
        'document_back',
        'selfie',
        'status',
        'reject_reason',
        'rejection_history',
        'verified_at'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'verified_at' => 'datetime',
        'rejection_history' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Добавляем запись в историю отклонений
    public function addRejectionToHistory()
    {
        $history = $this->rejection_history ?? [];
        $history[] = [
            'reason' => $this->reject_reason,
            'date' => now()->toDateTimeString()
        ];
        $this->rejection_history = $history;
        $this->save();
    }
}
