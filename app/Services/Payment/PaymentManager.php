<?php

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentContract;

class PaymentManager
{
    protected array $drivers = [];

    /**
     * @param $name
     * @param PaymentContract $driver
     * @return void
     */
    public function registerDriver($name, PaymentContract $driver): void
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
