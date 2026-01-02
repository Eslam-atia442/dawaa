<?php

namespace App\Services;

use App\Repositories\Contracts\ActivityLogContract;
use App\Repositories\Contracts\BaseContract;

class ActivityLogService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(ActivityLogContract $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }


}
