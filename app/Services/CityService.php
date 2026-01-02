<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\CityContract;
use Exception;
use Illuminate\Support\Facades\DB;

class CityService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(CityContract $repository)
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

    public function update($city, $request)
    {
        return $this->repository->update($city, $request);
    }

    public function remove($city)
    {
        return $this->repository->remove($city);
    }

}
