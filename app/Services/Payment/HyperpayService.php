<?php

namespace App\Services\Payment;

use App\Enums\PaymentStatusEnum;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Services\Payment\Contracts\PaymentContract;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class HyperpayService implements PaymentContract
{

    private string $hyperpay_checkouts_url;
    public string $hyperpay_base_url;
    private string $hyperpay_token;
    private string $hyperpay_credit_id;
    private string $hyperpay_mada_id;
    private string $hyperpay_apple_id;
    public string $currency;
    public string $mode;
    public string $driver = 'hyperpay';

    public function __construct()
    {
        $this->hyperpay_checkouts_url = config('services.hyperpay.hyperpay_checkouts_url');
        $this->hyperpay_base_url = config('services.hyperpay.hyperpay_base_url');
        $this->hyperpay_token = config('services.hyperpay.hyperpay_token');
        $this->currency = config('services.hyperpay.hyperpay_currncy');
        $this->hyperpay_credit_id = config('services.hyperpay.hyperpay_credit_id');
        $this->hyperpay_mada_id = config('services.hyperpay.hyperpay_mada_id');
        $this->hyperpay_apple_id = config('services.hyperpay.hyperpay_apple_id');
        $this->mode = config('services.hyperpay.mode');
    }

    /**
     * @param $data
     * @return RedirectResponse
     */
    public function processRequest($data): RedirectResponse
    {
        $postData = $this->getPayLoadData($data);
        $responseData = $this->sendRequest($postData);
        $redirectUrl = route('getPaymentForm', ['method' => $data['payment_method'], 'driver' => $this->driver, 'paymentId' => json_decode($responseData)->id]);
        return redirect()->to($redirectUrl);

    }

    /**
     * @param $data
     * @return array
     */
    public function successTransaction($data): array
    {
        $status = PaymentStatusEnum::paid->value;
        $payment_response = $this->fetchPayment($data['id'], $data['method']);
        if (!$this->paymentSuccess($payment_response['result']['code'])) {
            PaymentStatusEnum::values();
            $status = PaymentStatusEnum::failed->value;
        }
        return array_merge($data, [
            'payment_id' => $data['id'],
            'paid_at' => Carbon::now(),
            'invoice_id' => $payment_response['merchantTransactionId'],
            'status' => $status,
            'comment' => $payment_response['result']['description'],
            'trace_id' => $payment_response['merchantTransactionId'],
            'data' => $data,
        ]);
    }

    /**
     * @param $data
     * @return array
     */
    public function errorTransaction($data): array
    {

    }


    /**
     * @param $traceId
     * @return array
     */
    public function checkPaymentStatus($traceId): array
    {

    }

    private function getEntityId($method)
    {

        switch ($method) {
            case "CREDIT":
                return $this->hyperpay_credit_id;
            case "MADA":
                return $this->hyperpay_mada_id;
            case "APPLE":
                return $this->hyperpay_apple_id;
            default:
                return "";
        }
    }


    protected function getPayLoadData($data): array
    {
        $total = number_format($data['amount'], 2, '.', '');
        $entityId = $this->getEntityId($data['payment_method']);
        $postData = [
            'entityId' => $entityId,
            'amount' => $total,
            'currency' => $data['currency'] ?? $this->currency,
            'paymentType' => 'DB',
            'merchantTransactionId' => $data['trace_id'],
            'billing.street1' => 'riyadh',
            'billing.city' => 'riyadh',
            'billing.state' => 'riyadh',
            'billing.country' => 'SA',
            'billing.postcode' => '123456',
            'customer.email' => $data['email'],
            'customer.givenName' => $data['name'],
            'customer.surname' => $data['name'],
        ];

        if ($this->mode == "test" && $data['payment_method'] != "MADA") {
            $postData['testMode'] = 'EXTERNAL';
            $postData['customParameters[3DS2_enrolled]'] = 'true';
        }

        return $postData;
    }

    protected function sendRequest($postData): mixed
    {
        $response = Http::withQueryParameters($postData)->withHeaders([
            'Authorization' => 'Bearer ' . $this->hyperpay_token,
        ])->post($this->hyperpay_checkouts_url);
        return $response->body();
    }

    protected function fetchPayment($id, $method)
    {
        $entityId = $this->getEntityId($method);
        $url = config('services.hyperpay.hyper_url') . "/checkouts/" . $id . "/payment";

        $url .= "?entityId={$entityId}";
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->hyperpay_token
        ])->get($url);
        return $response->json();
    }

    public function paymentSuccess($responseCode): bool
    {
        if (preg_match("/^(000.000.|000.100.1|000.[36]|000.400.[1][12]0)/", $responseCode)
            || preg_match("/^(000.400.0[^3]|000.400.100)/", $responseCode)
        ) return true;

        return false;
    }
}
