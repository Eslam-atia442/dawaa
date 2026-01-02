<?php

namespace App\Services\SMS;

use App\Services\SMS\Contracts\SMSContract;

class SMSManager
{
    protected array $drivers = [];

    /**
     * @param $name
     * @param SMSContract $driver
     * @return void
     */
    public function registerDriver($name, SMSContract $driver): void
    {
        $this->drivers[$name] = $driver;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function driver($name): mixed
    {
        if (isset($this->drivers[$name])) {
            return $this->drivers[$name];
        }

        throw new \InvalidArgumentException("Driver [$name] not supported.");
    }
}
