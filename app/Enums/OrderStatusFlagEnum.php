<?php

namespace App\Enums;

enum OrderStatusFlagEnum: int
{
    case IN_PROGRESS = 1;
    case DELIVERED = 2;
    case REJECTED = 3;
    case CANCELED = 4;

    public static function getTranslation(int $value): string
    {
        return match ($value) {
            self::IN_PROGRESS->value => __('trans.order_status_in_progress'),
            self::DELIVERED->value => __('trans.order_status_delivered'),
            self::REJECTED->value => __('trans.order_status_rejected'),
            self::CANCELED->value => __('trans.order_status_canceled'),
            default => __('trans.order_status_in_progress'),
        };
    }

    public static function getColor(int $value): string
    {
        return match ($value) {
            self::IN_PROGRESS->value => 'primary',
            self::DELIVERED->value => 'success',
            self::REJECTED->value => 'danger',
            self::CANCELED->value => 'secondary',
            default => 'primary',
        };
    }

    public function label(): string
    {
        return self::getTranslation($this->value);
    }

    public function color(): string
    {
        return self::getColor($this->value);
    }
}
