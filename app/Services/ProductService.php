<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\ProductContract;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(ProductContract $repository)
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

    public function update($product, $request)
    {
        return $this->repository->update($product, $request);
    }

    public function remove($product)
    {
        return $this->repository->remove($product);
    }

}
