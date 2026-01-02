<?php

namespace App\Services\Payment\Contracts;

use Illuminate\Http\RedirectResponse;

interface PaymentContract
{
    public function processRequest($data): bool|RedirectResponse;
    public function checkPaymentStatus($payment): array;
    public function successTransaction($data): array;
    public function errorTransaction($data): array;
}
