<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category',
        'amount',
        'currency',
        'description',
        'receipt_file',
        'status',
        'rejection_reason',
        'expense_date'
    ];

    protected $dates = [
        'expense_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
