<?php

namespace App\Services\SMS;

use App\Services\SMS\Contracts\SMSContract;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Msegat implements SMSContract
{
    protected string $username;
    protected string $apiKey;
    protected string $userSender;
    protected string $url;

    public function __construct()
    {
        $this->username = config('services.msegat.username');
        $this->apiKey = config('services.msegat.apiKey');
        $this->userSender = config('services.msegat.userSender');
        $this->url = config('services.msegat.url');
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
            $numbersString = implode(",", $numbers);
            $response = $this->sendSms($numbersString, $message);
            return $this->handleResponse($response);
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
            "userName" => $this->username,
            "numbers" => "$number",
            "userSender" => $this->userSender,
            "apiKey" => $this->apiKey,
            "msg" => "$message"
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
        if (!$response['message'] == "success") {
            logger()->channel('gateways.sms.fail')->info("Failed to send SMS. Error: " . $response->body());
            return false;
        }
        return true;
    }
}
