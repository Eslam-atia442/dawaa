<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\OrderContract;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(OrderContract $repository)
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

    public function update($order, $request)
    {
        return $this->repository->update($order, $request);
    }

    public function remove($order)
    {
        return $this->repository->remove($order);
    }

}
