<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatusEnum;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Http\Resources\PaymentResource;

class PaymentController extends BaseApiController
{

    public string $driver  = 'hyperpay';//will get from setting configuration
    public function __construct(PaymentService $service){
        $this->service = $service;
        parent::__construct($service, PaymentResource::class);
    }

    public function payInvoice(Request $request)
    {

      return $this->service->initiatePayment($this->driver , [
            'name' => "Eslam atia",
            'amount' => $request->amount??10,
            'email' => "engeslamatia100@gmail.com",
            'trace_id' => mt_rand(),
            'status' => PaymentStatusEnum::initial->value,
            'payment_method' => $request->payment_method ?? "CREDIT",

        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function successCallback(Request $request)
    {
        $data = $this->service->successTransaction($this->driver, $request->all());
        return $this->respondWithModel($data);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function errorCallback(Request $request):mixed
    {
       $data = $this->service->errorTransaction($this->driver, $request->all());
        return $this->respondWithModel($data);
    }
    public function checkPaymentStatus($paymentId){
        return $this->service->checkPaymentStatus($this->driver, $paymentId);
    }

    public function getPaymentForm(Request $request)
    {
        $driver = $request->input('driver');
        $paymentId = $request->input('paymentId');
        $method = $request->input('method');

        if ($driver === "hyperpay") {
            if ($paymentId && $method) {
                return view('payments.hyper_payment_form', compact('paymentId', 'method'));
            } else {
                return response()->json(['error' => 'Payment ID and method are required.'], 400);
            }
        } else {
            return response()->json(['error' => 'Unsupported driver.'], 400);
        }
    }


}
