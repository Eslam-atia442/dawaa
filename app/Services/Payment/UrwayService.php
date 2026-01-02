<?php

namespace App\Services\Payment;

use Carbon\Carbon;
use App\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Services\Payment\Contracts\PaymentContract;

class UrwayService implements PaymentContract
{

    protected string $baseUrl;
    protected string $terminalId;
    protected string $password;
    protected string $merchantKey;
    protected string $apiKey;
    protected string $currency;
    protected string $language = 'en';
    protected string $city = 'Riyadh';
    protected string $countryCode = 'SA';
    protected string $callbackUrl;
    protected string $actionCreatePayment = "1";
    protected string $actionCheckPaymentStatus = "10";
    protected $endpoint = 'URWAYPGService/transaction/jsonProcess/JSONrequest';

    public function __construct()
    {
        $this->baseUrl = config('services.urway.base_url');
        $this->terminalId = config('services.urway.terminal_id');
        $this->password = config('services.urway.password');
        $this->merchantKey = config('services.urway.merchant_key');
        $this->currency = config('services.urway.currency');
        $this->callbackUrl = config("services.urway.callback_url");
    }

    /**
     * @param $data
     * @return false|RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processRequest($data): bool|RedirectResponse
    {
        $response = Http::acceptJson()->post("{$this->baseUrl}/{$this->endpoint}", $this->getPayLoadData($data));
        $result = json_decode($response->getBody(), true);

        if ($response->getStatusCode() != 200) {
            return false;
        }

        return Redirect::away($result['targetUrl'] . '?paymentid=' . $result['payid']);
    }

    /**
     * @param $data
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function successTransaction($data): array
    {
        $status = PaymentStatusEnum::failed->value;

        if ($data['Result'] == 'Successful') {
            $status = PaymentStatusEnum::paid->value;
        }

        return array_merge($data, [
            'payment_id' => $data['PaymentId'],
            'paid_at' => Carbon::now(),
            'invoice_id' => $data['TrackId'],
            'status' => $status,
            'comment' => $this->getFailResponseMessage($data['ResponseCode']),
            'trace_id' => $data['TrackId'],
            'data' => $data,
        ]);
    }

    /**
     * @param $data
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function errorTransaction($data): array
    {

    }

    /**
     * @param $data
     * @return array
     */
    private function getPayLoadData($data): array
    {
        return [
            "amount" => $data['amount'],
            "address" => $data['address'] ?? $this->city,// customer address
            "customerIp" => $this->getServerIp(), // Customer IP
            "city" => $data['city'] ?? $this->city,
            "trackid" => $data['trace_id'], //  Order ID
            "terminalId" => $this->terminalId,
            "action" => $data['action'] ?? $this->actionCreatePayment,
            "password" => $this->password, // Provided by URWAY
            "merchantIp" => $this->getServerIp(), // Merchant server IP
            "requestHash" => $this->hashedData($data), // secret_key Per order Created using SHA256 online
            "country" => $data['country_code'] ?? $this->countryCode,  //  country code
            "currency" => $data['currency']??$this->currency,// Currency code
            "customerEmail" => "",
            "zipCode" => "",
            "udf1" => "",
            "udf2" => url("/{$this->callbackUrl}"),//Callback URL
            "udf3" => $data['language'] ?? $this->language,//AR / EN
            "udf4" => "",//refer the table below
            "udf5" => "",
        ];
    }

    /**
     * @param $traceId
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkPaymentStatus($payment): array
    {
        $payment['action'] = $this->actionCheckPaymentStatus;
        $data = $this->getPayLoadData($payment);
        $data['transid'] = $payment->payment_id;
        $response = Http::acceptJson()->post("{$this->baseUrl}/{$this->endpoint}", $data);
        return json_decode($response->getBody(), true);
    }

    /**
     * @return string
     */
    private function getServerIp(): string
    {
        return (string)$_SERVER['SERVER_ADDR'];
    }

    /**
     * @param $data
     * @return string
     */
    private function hashedData($data): string
    {
        $requestHash = $data['trace_id'] . '|' . config('services.urway.terminal_id') . '|' . $this->password . '|' . $this->merchantKey . '|' . $data['amount'] . '|' . ($data['currency'] ?? $this->currency);
        return hash('sha256', $requestHash);
    }

    private function responseStatus(): array
    {
        return [
            "000" => "Transaction Successful",
            "601" => "System Error, Please contact System Admin",
            "659" => "Request authentication failed",
            "701" => "Error while processing ApplePay payment Token request",
            "906" => "Invalid Card Token",
            "514" => "Bank Rejections",
        ];
    }

    /**
     * @param $status
     * @return string
     */
    private function getFailResponseMessage($status): string
    {
        return array_key_exists($status, $this->responseStatus()) ?
            $this->responseStatus()[$status]
            : "Bank Rejections";

    }
}
