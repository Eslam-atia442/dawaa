<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\StoreContract;
use Exception;
use Illuminate\Support\Facades\DB;

class StoreService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(StoreContract $repository)
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

    public function update($store, $request)
    {
        return $this->repository->update($store, $request);
    }

    public function remove($store)
    {
        return $this->repository->remove($store);
    }

}
