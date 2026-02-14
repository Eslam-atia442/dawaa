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
    case REFUND_REQUESTED = 6;
    case REFUND_APPROVED = 7;
    case REFUND_REJECTED = 8;
    case PAID = 9;

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
            self::REFUND_REQUESTED->value => __('trans.refund_requested'),
            self::REFUND_APPROVED->value => __('trans.refund_approved'),
            self::REFUND_REJECTED->value => __('trans.refund_rejected'),
            self::PAID->value => __('trans.paid'),
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
            self::REFUND_REQUESTED->value => __('trans.refund_requested'),
            self::REFUND_APPROVED->value => __('trans.refund_approved'),
            self::REFUND_REJECTED->value => __('trans.refund_rejected'),
            self::PAID->value => __('trans.paid'),
            default => ''
        };
    }
}
