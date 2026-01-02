<?php

namespace App\Services;

use App\Models\Payment as PaymentModel;
use App\Facades\Payment;
use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\PaymentContract;

class PaymentService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(PaymentContract $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    /**
     * @param $driver
     * @param array $data
     * @return mixed
     */
    public function initiatePayment($driver, array $data): mixed
    {
        $this->repository->create($data);
        return Payment::driver($driver)->processRequest($data);
    }

    /**
     * @param $driver
     * @param $data
     * @return PaymentModel
     */
    public function successTransaction($driver, $data): PaymentModel
    {
        $results = Payment::driver($driver)->successTransaction($data);
        $payment = $this->repository->findBy('trace_id', $results['trace_id']);
        $this->repository->update($payment, $results);
        return $payment;
    }

    /**
     * @param $driver
     * @param $data
     * @return PaymentModel
     */
    public function errorTransaction($driver, $data): PaymentModel
    {
        $results = Payment::driver($driver)->errorTransaction($data);
        $payment = $this->repository->findBy('trace_id', $results['trace_id']);
        $this->repository->update($payment, $results);

        return $payment;
    }

    /**
     * @param $driver
     * @param $traceId
     * @return mixed
     */
    public function checkPaymentStatus($driver, $traceId): mixed
    {
        $payment = $this->repository->findBy('trace_id', $traceId);
        return Payment::driver($driver)->checkPaymentStatus($payment);
    }

}
