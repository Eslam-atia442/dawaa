<?php

namespace App\Enums;

use App\Traits\ConstantsTrait;

enum TransactionTypeEnum: int
{
    use ConstantsTrait;

    case WALLET_DEPOSIT = 1;
    case WALLET_REFUND = 2;
    case CASH_DEPOSIT = 3;
    case CASH_REFUND = 4;
    case ONLINE_DEPOSIT = 5;
    case ONLINE_REFUND = 6;

    public function label(): string
    {
        return $this->getLabels()[$this->value];
    }

    public function getLabels(): array
    {
        return [
            self::WALLET_DEPOSIT->value => __('trans.wallet_deposit'),
            self::WALLET_REFUND->value => __('trans.wallet_refund'),
            self::CASH_DEPOSIT->value => __('trans.cash_deposit'),
            self::CASH_REFUND->value => __('trans.cash_refund'),
            self::ONLINE_DEPOSIT->value => __('trans.online_deposit'),
            self::ONLINE_REFUND->value => __('trans.online_refund'),
        ];
    }

    public static function getLabel($value): string
    {
        return match ($value) {
            self::WALLET_DEPOSIT->value => __('trans.wallet_deposit'),
            self::WALLET_REFUND->value => __('trans.wallet_refund'),
            self::CASH_DEPOSIT->value => __('trans.cash_deposit'),
            self::CASH_REFUND->value => __('trans.cash_refund'),
            self::ONLINE_DEPOSIT->value => __('trans.online_deposit'),
            self::ONLINE_REFUND->value => __('trans.online_refund'),
            default => ''
        };
    }
}
