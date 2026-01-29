<?php

namespace App\Repositories\Contracts;

interface OrderContract extends BaseContract
{
    public function getUserOrders($userId, $filters = [], $relations = []);
}