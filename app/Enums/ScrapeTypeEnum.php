<?php

namespace App\Enums;

use App\Traits\ConstantsTrait;

enum ScrapeTypeEnum: int
{
    use ConstantsTrait;

    case azimut     = 1;
    case beltone    = 2;
    case afim       = 3;
    case banquemisr = 4;
    case zeed       = 5;

    public function label(): string{
        return $this->getLabels()[$this->value];
    }

    public function getLabels(): array{
        return [
            self::azimut->value     => __('trans.azimut'),
            self::beltone->value    => __('trans.beltone'),
            self::afim->value       => __('trans.afim'),
            self::banquemisr->value => __('trans.banquemisr'),
            self::zeed->value       => __('trans.zeed'),
        ];
    }

    public static function getLabel($value): string{

        return match ($value) {
            self::azimut->value     => __('trans.azimut'),
            self::beltone->value    => __('trans.beltone'),
            self::afim->value       => __('trans.afim'),
            self::banquemisr->value => __('trans.banquemisr'),
            self::zeed->value       => __('trans.zeed'),
            default                 => ''
        };
    }
}
