<?php

namespace App\Services\SMS\Contracts;

interface SMSContract
{

    public function send(string $number, string $message):bool;
    public function sendBulkSMS(array $numbers, string $message): bool;
}
