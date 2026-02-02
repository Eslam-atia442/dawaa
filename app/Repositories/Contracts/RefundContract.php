<?php

namespace App\Repositories\Contracts;

interface RefundContract extends BaseContract
{
    public function createRefundOrder($originalOrder, $refundData);
    public function getRefundableOrders($userId, $filters = []);
}