<?php

namespace App\Enums;

use App\Traits\ConstantsTrait;

enum TransactionStatusEnum: int
{
    use ConstantsTrait;

    case PENDING = 1;
    case COMPLETED = 2;
    case FAILED = 3;
    case CANCELLED = 4;
    case REFUNDED = 5;

    public function label(): string
    {
        return $this->getLabels()[$this->value];
    }

    public function getLabels(): array
    {
        return [
            self::PENDING->value => __('trans.pending'),
            self::COMPLETED->value => __('trans.completed'),
            self::FAILED->value => __('trans.failed'),
            self::CANCELLED->value => __('trans.cancelled'),
            self::REFUNDED->value => __('trans.refunded'),
        ];
    }

    public static function getLabel($value): string
    {
        return match ($value) {
            self::PENDING->value => __('trans.pending'),
            self::COMPLETED->value => __('trans.completed'),
            self::FAILED->value => __('trans.failed'),
            self::CANCELLED->value => __('trans.cancelled'),
            self::REFUNDED->value => __('trans.refunded'),
            default => ''
        };
    }
}
