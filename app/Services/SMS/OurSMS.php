<?php

namespace App\Services\SMS;

use App\Services\SMS\Contracts\SMSContract;
use Illuminate\Support\Facades\Http;
use \Illuminate\Http\Client\Response;

class OurSMS implements SMSContract
{
    protected string $token;
    protected string $source;
    protected string $url;

    public function __construct()
    {
        $this->token = config('services.oursms.token');
        $this->source = config('services.oursms.source');
        $this->url = config('services.oursms.url');
    }

    /**
     * Send a single SMS.
     *
     * @param string $number
     * @param string $message
     * @return bool
     */
    public function send(string $number, string $message): bool
    {
        try {
            $response = $this->sendSms($number, $message);
            return $this->handleResponse($response);
        } catch (\Exception $e) {
            logger()->channel('gateways.sms.fail')->info("Failed to send SMS: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Send bulk SMS to multiple numbers.
     *
     * @param array $numbers
     * @param string $message
     * @return bool
     */
    public function sendBulkSMS(array $numbers, string $message): bool
    {
        try {
            foreach ($numbers as $number) {
                $response = $this->sendSms($number, $message);
                return $this->handleResponse($response);
            }
            return true;
        } catch (\Exception $e) {
            logger()->channel('gateways.sms.fail')->info("Failed to send SMS: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Send SMS request to the API.
     *
     * @param string $number
     * @param string $message
     * @return mixed
     */
    private function sendSms(string $number, string $message): mixed
    {
        return Http::post($this->url, [
            'token' => $this->token,
            'src' => $this->source,
            'dests' => $number,
            'body' => $message,
        ]);
    }

    /**
     * Handle the HTTP response from sending SMS.
     *
     * @param Response $response
     * @return bool
     */
    private function handleResponse(Response $response): bool
    {
        if (!$response->successful()) {
            logger()->channel('gateways.sms.fail')->info("Failed to send SMS. Error: " . $response->body());
            return false;
        }
        return true;
    }
}
