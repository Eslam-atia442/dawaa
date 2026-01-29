<?php

namespace App\Repositories\SQL;

use App\Models\Order;
use App\Repositories\Contracts\OrderContract;

class OrderRepository extends BaseRepository implements OrderContract
{
    /**
     * OrderRepository constructor.
     * @param Order $model
     */
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }
}
