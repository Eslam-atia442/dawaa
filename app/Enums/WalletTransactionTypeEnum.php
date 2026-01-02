<?php

namespace App\Enums;

use App\Traits\ConstantsTrait;

enum WalletTransactionTypeEnum: int
{
    use ConstantsTrait;

    case add = 1;
    case deduct = 2;


    public function label(): string
    {
        return $this->getLabels()[$this->value];
    }

    public function getLabels(): array
    {
        return [
            self::add->value => __('add'),
            self::deduct->value => __('deduct'),
        ];
    }
}
