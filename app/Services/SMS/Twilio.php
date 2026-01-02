<?php

namespace App\Services\SMS;

use Twilio\Rest\Client;
use App\Services\SMS\Contracts\SMSContract;

class Twilio implements SMSContract
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.account_sid'),
            config('services.twilio.auth_token')
        );
    }


    public function send(string $number, string $message): bool
    {
        //::todo handle error status
       $this->client->messages->create(
            $number,
            [
                'from' => config('services.twilio.from_number'),
                'body' => $message,
            ]
        );
      return true;
    }

    public function sendBulkSMS(array $numbers, string $message): bool
    {

    }

}
