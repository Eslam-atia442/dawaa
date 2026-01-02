<?php

namespace App\Enums;

use App\Traits\ConstantsTrait;

enum PriceDirectionEnum: int
{
    use ConstantsTrait;

    case same = 0;
    case up = 1;
    case down = 2;

    public function label(): string
    {
        return $this->getLabels()[$this->value];
    }

    public function getLabels(): array
    {
        return [
            self::same->value => __('same'),
            self::up->value => __('up'),
            self::down->value => __('down'),
        ];
    }
}


