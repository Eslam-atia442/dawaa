<?php

namespace App\Repositories\SQL;

use App\Models\Store;
use App\Repositories\Contracts\StoreContract;

class StoreRepository extends BaseRepository implements StoreContract
{
    /**
     * StoreRepository constructor.
     * @param Store $model
     */
    public function __construct(Store $model)
    {
        parent::__construct($model);
    }
}
