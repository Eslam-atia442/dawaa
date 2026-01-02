<?php

namespace App\Enums;
use App\Traits\ConstantsTrait;

enum FundCategoryTypeEnum : int
{
    use ConstantsTrait;

    case GOLD = 1;
    case STOCKS = 2;
    case REAL_ESTATE = 3;
    case CASH = 4;

    public function label():string
    {
        return $this->getLabels()[$this->value];
    }

    public function getLabels():array
    {
        return [
            self::GOLD->value => __('trans.gold'),
            self::STOCKS->value => __('trans.stocks'),
            self::REAL_ESTATE->value => __('trans.real_estate'),
            self::CASH->value => __('trans.cash')
        ];
    }

    public static function getLabel($value):string
    {
        return match ($value) {
            self::GOLD->value => __('trans.gold'),
            self::STOCKS->value => __('trans.stocks'),
            self::REAL_ESTATE->value => __('trans.real_estate'),
            self::CASH->value => __('trans.cash'),
            default => ''
        };
    }

}
