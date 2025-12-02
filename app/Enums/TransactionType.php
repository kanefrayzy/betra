<?php

namespace App\Enums;

enum TransactionType: string
{
    case Bet = 'bet';
    case Win = 'win';
    case Deposit = 'payment';
    case Withdrawal = 'withdrawal';
    case Refund = 'refund';
    case Bonus = 'bonus';
    case Rollback = 'rollback';
    case Transfer = 'transfer';
    case Rain = 'rain';
    case DailyBonus = 'dailybonus';
    case TelegramBonus = 'telegram_bonus';

}
