<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\TransactionType;
use App\Enums\WithdrawalStatus;

class CommentGenerator
{
    public static function transaction(TransactionType $type, float $amount, string $currency): string
    {
        $amount = moneyFormat($amount);
        return match ($type) {
            TransactionType::Bet => "Bet of $amount $currency placed.",
            TransactionType::Win => "Win of $amount $currency recorded.",
            TransactionType::Deposit => "Deposit of $amount $currency placed.",
            TransactionType::Withdrawal => "Withdrawal of $amount $currency initiated.",
            TransactionType::Refund => "Refund of $amount $currency processed.",
            TransactionType::Bonus => "Bonus of $amount $currency granted.",
            TransactionType::Rollback => "Rollback of $amount $currency completed.",
            TransactionType::Transfer => "Transfer of $amount $currency made.",
            TransactionType::Rain => "Rain of $amount $currency distributed.",
            default => "Transaction of $amount $currency processed.",
        };
    }


    public static function payment(PaymentStatus $status, float $amount, string $currency): string
    {
        $amount = moneyFormat($amount);
        return match ($status) {
            PaymentStatus::Pending => "Payment of $amount $currency is pending.",
            PaymentStatus::Completed => "Payment of $amount $currency completed successfully.",
            PaymentStatus::Failed => "Payment of $amount $currency failed.",
            PaymentStatus::Canceled => "Payment of $amount $currency canceled.",
            PaymentStatus::Refunded => "Payment of $amount $currency refunded.",
            default => "Payment of $amount $currency processed.",
        };
    }

    public static function withdrawal(float $amount, string $currency, WithdrawalStatus $status): string
    {
        $amount = moneyFormat($amount);
        return match ($status) {
            WithdrawalStatus::Pending => "Withdrawal of $amount $currency is pending.",
            WithdrawalStatus::Completed => "Withdrawal of $amount $currency completed successfully.",
            WithdrawalStatus::Failed => "Withdrawal of $amount $currency failed.",
            WithdrawalStatus::Canceled => "Withdrawal of $amount $currency canceled.",
            default => "Withdrawal of $amount $currency processed.",
        };
    }


}
