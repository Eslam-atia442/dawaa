<?php

namespace App\Repositories\SQL;

use App\Models\ProductQuantityHistory;
use App\Repositories\Contracts\ProductQuantityHistoryContract;

class ProductQuantityHistoryRepository extends BaseRepository implements ProductQuantityHistoryContract
{
    public function __construct(ProductQuantityHistory $model)
    {
        parent::__construct($model);
    }
}
