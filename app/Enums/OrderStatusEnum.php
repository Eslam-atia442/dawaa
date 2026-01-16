<?php

namespace App\Enums;

use App\Traits\ConstantsTrait;

enum OrderStatusEnum: int
{
    use ConstantsTrait;

    case PENDING = 1;
    case PROCESSING = 2;
    case SHIPPED = 3;
    case DELIVERED = 4;
    case CANCELLED = 5;

    public function label(): string
    {
        return $this->getLabels()[$this->value];
    }

    public function getLabels(): array
    {
        return [
            self::PENDING->value => __('trans.pending'),
            self::PROCESSING->value => __('trans.processing'),
            self::SHIPPED->value => __('trans.shipped'),
            self::DELIVERED->value => __('trans.delivered'),
            self::CANCELLED->value => __('trans.cancelled'),
        ];
    }

    public static function getLabel($value): string
    {
        return match ($value) {
            self::PENDING->value => __('trans.pending'),
            self::PROCESSING->value => __('trans.processing'),
            self::SHIPPED->value => __('trans.shipped'),
            self::DELIVERED->value => __('trans.delivered'),
            self::CANCELLED->value => __('trans.cancelled'),
            default => ''
        };
    }
}
