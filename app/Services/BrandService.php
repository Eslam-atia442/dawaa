<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\BrandContract;
use Exception;
use Illuminate\Support\Facades\DB;

class BrandService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(BrandContract $repository)
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

    public function update($brand, $request)
    {
        return $this->repository->update($brand, $request);
    }

    public function remove($brand)
    {
        return $this->repository->remove($brand);
    }

}
