<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\ChildProductContract;
use Exception;
use Illuminate\Support\Facades\DB;

class ChildProductService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(ChildProductContract $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    public function create($request)
    {
        DB::beginTransaction();
        $object = $this->repository->create($request);
        DB::commit();
        return $object;
    }

    public function update($childProduct, $request)
    {
        return $this->repository->update($childProduct, $request);
    }

    public function remove($childProduct)
    {
        return $this->repository->remove($childProduct);
    }

}
