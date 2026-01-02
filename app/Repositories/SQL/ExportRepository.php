<?php

namespace App\Repositories\SQL;

use App\Models\Export;
use App\Repositories\Contracts\ExportContract;

class ExportRepository extends BaseRepository implements ExportContract
{
    public function __construct(Export $model)
    {
        parent::__construct($model);
    }
}
