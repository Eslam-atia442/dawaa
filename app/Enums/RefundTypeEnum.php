<?php

namespace App\Enums;

use App\Traits\ConstantsTrait;

enum RefundTypeEnum: int
{
    use ConstantsTrait;

    case CASH = 1;
    case WALLET = 2;
    case ONLINE = 3;

    public function label(): string
    {
        return $this->getLabels()[$this->value];
    }

    public function getLabels(): array
    {
        return [
            self::CASH->value => __('trans.cash'),
            self::WALLET->value => __('trans.wallet'),
            self::ONLINE->value => __('trans.online'),
        ];
    }

    public static function getLabel($value): string
    {
        return match ($value) {
            self::CASH->value => __('trans.cash'),
            self::WALLET->value => __('trans.wallet'),
            self::ONLINE->value => __('trans.online'),
            default => ''
        };
    }
}
