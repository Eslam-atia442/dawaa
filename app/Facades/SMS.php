<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\SMS\Contracts\SMSContract;

class SMS extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return SMSContract::class;
    }
}
