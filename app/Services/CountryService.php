<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\CountryContract;
use Exception;
use Illuminate\Support\Facades\DB;

class CountryService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(CountryContract $repository)
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

    public function update($country, $request)
    {
        return $this->repository->update($country, $request);
    }

    public function remove($country)
    {
        return $this->repository->remove($country);
    }

}
