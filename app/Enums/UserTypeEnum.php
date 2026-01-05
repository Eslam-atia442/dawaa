<?php

namespace App\Enums;

use App\Traits\ConstantsTrait;

enum UserTypeEnum: int
{
    use ConstantsTrait;

    case DOCTOR = 1;
    case PHARMACY = 2;

    public function label(): string
    {
        return $this->getLabels()[$this->value];
    }

    public function getLabels(): array
    {
        return [
            self::DOCTOR->value => __('trans.doctor'),
            self::PHARMACY->value => __('trans.pharmacy')
        ];
    }

    public static function getLabel($value): string
    {
        return match ($value) {
            self::DOCTOR->value => __('trans.doctor'),
            self::PHARMACY->value => __('trans.pharmacy'),
            default => ''
        };
    }
}

