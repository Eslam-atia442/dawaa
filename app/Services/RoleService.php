<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\PermissionContract;
use App\Repositories\Contracts\RoleContract;
use Exception;
use Illuminate\Support\Facades\DB;

class RoleService extends BaseService
{

    protected BaseContract $repository;

    protected PermissionContract $permissionRepository;

    public function __construct(RoleContract $repository, PermissionContract $permissionRepository)
    {
        $this->repository = $repository;
        $this->permissionRepository = $permissionRepository;
        parent::__construct($repository);
    }

    public function create($request)
    {
        DB::beginTransaction();
        $object = $this->repository->create($request);
        DB::commit();
        return $object;
    }
}
