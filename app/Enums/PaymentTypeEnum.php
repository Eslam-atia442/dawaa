<?php

namespace App\Enums;

use App\Traits\ConstantsTrait;

enum PaymentTypeEnum: int
{
    use ConstantsTrait;


    case ONLINE = 1;
    case CASH = 2;
    case WALLET = 3;

    public function label(): string
    {
        return $this->getLabels()[$this->value];
    }

    public function getLabels(): array
    {
        return [
            self::ONLINE->value => __('trans.online'),
            self::CASH->value => __('trans.cash'),
            self::WALLET->value => __('trans.wallet'),
        ];
    }

    public static function getLabel($value): string
    {
        return match ($value) {
            self::ONLINE->value => __('trans.online'),
            self::CASH->value => __('trans.cash'),
            self::WALLET->value => __('trans.wallet'),
            default => ''
        };
    }
}
