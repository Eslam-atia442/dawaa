<?php

namespace App\Repositories\SQL;

use App\Models\Order;
use App\Repositories\Contracts\RefundContract;

class RefundRepository extends BaseRepository implements RefundContract
{
    /**
     * RefundRepository constructor.
     * @param Order $model
     */
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function createRefundOrder($originalOrder, $refundData)
    {
        // Create refund order linked to original order
        $refundOrderData = array_merge($refundData, [
            'parent_id' => $originalOrder->id,
            'user_id' => $originalOrder->user_id,
        ]);

        return $this->create($refundOrderData);
    }


}