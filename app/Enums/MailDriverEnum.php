<?php

namespace App\Enums;
use App\Traits\ConstantsTrait;

enum MailDriverEnum : int
{
    use ConstantsTrait;

    case admin = 1;
    case user = 2;

    public function label():string
    {
        return $this->getLabels()[$this->value];
    }

    public function getLabels():array
    {
        return [
            self::admin->value => __('admin'),
            self::user->value => __('user'),
        ];
    }
}
