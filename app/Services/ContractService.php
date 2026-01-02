<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\ContractContract;
use Exception;
use Illuminate\Support\Facades\DB;

class ContractService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(ContractContract $repository)
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

    public function update($contract, $request)
    {
        return $this->repository->update($contract, $request);
    }

    public function remove($contract)
    {
        return $this->repository->remove($contract);
    }

}
