<?php

namespace App\Services\Payment;

use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Services\Payment\Contracts\PaymentContract;

class MyFatoorahService implements PaymentContract
{

    protected string $baseUrl;
    protected string $apiKey;
    protected Client $client;
    protected string $currency;
    protected string $language = 'en';

    public function __construct()
    {
        $this->baseUrl  = config('services.myfatoorah.base_url');
        $this->apiKey   = config('services.myfatoorah.api_key');
        $this->currency = config('services.myfatoorah.currency');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
        ]);
    }

    /**
     * @param $data
     * @return false|RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processRequest($data): bool|RedirectResponse
    {
        $response = $this->client->post('SendPayment', [
            'json' => $this->getPayLoadData($data),
        ]);

        if ($response->getStatusCode() != 200) {
            return false;
        }
        $result = json_decode($response->getBody(), true);
        return Redirect::away($result['Data']['InvoiceURL']);
    }

    /**
     * @param $data
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function successTransaction($data): array
    {
        $response = $this->client->post('GetPaymentStatus', [
            'json' => [
                'key' => $data['paymentId'] ?? '',
                'keyType' => 'PaymentId'
            ],
        ]);
        $results = json_decode($response->getBody(), true);

        return array_merge($results['Data'], [
            'payment_id' => $data['paymentId'],
            'trace_id' => $results['Data']['CustomerReference'],
            'invoice_id' => $results['Data']['InvoiceId'],
            'paid_at'    => $results['Data']['CreatedDate'],
            'status'     => $results['Data']['InvoiceStatus'],
            'data'       => $results['Data'],
        ]);
    }

    /**
     * @param $data
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function errorTransaction($data): array
    {
        $response = $this->client->post('GetPaymentStatus', [
            'json' => [
                'key' => $data['paymentId'] ?? '',
                'keyType' => 'PaymentId'
            ],
        ]);
        $results = json_decode($response->getBody(), true);
        return array_merge($results['Data'], [
            'payment_id' => $data['paymentId'],
            'trace_id' => $results['Data']['CustomerReference'],
            'invoice_id' => $results['Data']['InvoiceId'],
            'paid_at'    => $results['Data']['CreatedDate'],
            'status'     => $results['Data']['InvoiceTransactions'][0]['TransactionStatus'],
            'comment'    => $results['Data']['InvoiceTransactions'][0]['Error'],
            'data'       => $results['Data'],
        ]);
    }

    /**
     * @param $data
     * @return array
     */
    private function getPayLoadData($data): array
    {
        return [
            'CustomerName' => $data['name'] ?? '',
            'InvoiceValue' => $data['amount'] ?? '',
            'DisplayCurrencyIso' => $data['currency'] ?? $this->currency,
            "NotificationOption" => "LNK",
            'CustomerEmail' => $data['email'] ?? 'example@gmail.com',
            'CallBackUrl' => url(config('services.myfatoorah.success_url')),
            'ErrorUrl' => url(config('services.myfatoorah.error_url')),
            'MobileCountryCode' => $data['phone_country_code'] ?? '',
            'CustomerMobile' => $data['phone_number'] ?? '',
            'Language' => $data['language'] ?? $this->language,
            'CustomerReference' => $data['trace_id'] ?? '',
            'SourceInfo' => env('app_name') . ' ' . app()::VERSION
        ];
    }

    /**
     * @param $traceId
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkPaymentStatus($payment): array
    {
        $response = $this->client->post('GetPaymentStatus', [
            'json' => [
                'key' => $payment->trace_id,
                'keyType' => 'CustomerReference'
            ],
        ]);
        $results = json_decode($response->getBody(), true);
        return $results['Data'];
    }
}
