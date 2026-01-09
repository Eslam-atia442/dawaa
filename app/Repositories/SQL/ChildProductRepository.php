<?php

namespace App\Repositories\SQL;

use App\Models\ChildProduct;
use App\Repositories\Contracts\ChildProductContract;

class ChildProductRepository extends BaseRepository implements ChildProductContract
{
    /**
     * ChildProductRepository constructor.
     * @param ChildProduct $model
     */
    public function __construct(ChildProduct $model)
    {
        parent::__construct($model);
    }
}
