<?php

namespace App\Services\SMS;

use Vonage\Client;
use Vonage\SMS\Message\SMS;
use Vonage\SMS\Collection;
use Vonage\Client\Credentials\Basic;
use App\Services\SMS\Contracts\SMSContract;

class Vonage implements SMSContract
{

    public $client;

    public function __construct()
    {
        $basic = new Basic(config('services.vonage.api_key'), config('services.vonage.api_secret'));
        $this->client = new Client($basic);
    }

    /**
     * @param string $number
     * @param string $message
     * @return bool
     */
    public function send(string $number, string $message): bool
    {
        try {
            $response = $this->sendSms($number, $message);
            $result = $response->current();
            return $result->getStatus() == 0;

        } catch (\Exception $e) {
            logger()->channel('gateways.sms.fail')->info("Failed to send SMS: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * @param array $numbers
     * @param string $message
     * @return bool
     */
    public function sendBulkSMS(array $numbers, string $message): bool
    {
        try {
            foreach ($numbers as $number) {
                $response = $this->sendSms($number, $message);
                $response->current();
            }
            return true;
        } catch (\Exception $e) {
            logger()->channel('gateways.sms.fail')->info("Failed to send bulk SMS: {$e->getMessage()}");
            return false;
        }
    }

    private function sendSms($number, $message): Collection
    {
        return $this->client->sms()->send(
            new SMS($number, env('APP_NAME'), $message, 'unicode')
        );
    }
}
