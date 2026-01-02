<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\RegionContract;
use Exception;
use Illuminate\Support\Facades\DB;

class RegionService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(RegionContract $repository)
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

    public function update($region, $request)
    {
        return $this->repository->update($region, $request);
    }

    public function remove($region)
    {
        return $this->repository->remove($region);
    }

}
