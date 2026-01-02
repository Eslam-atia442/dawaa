<?php

namespace App\Services;

use App\Models\Permission;
use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\FileContract;
use App\Repositories\Contracts\PermissionContract;

class PermissionService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(PermissionContract $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    public function toggleField($id, $field)
    {
        $modelObject = $this->repository->find($id);
        return $this->repository->toggleField($modelObject, $field);
    }


}
