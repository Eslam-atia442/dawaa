<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\SliderContract;
use Exception;
use Illuminate\Support\Facades\DB;

class SliderService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(SliderContract $repository)
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

    public function update($slider, $request)
    {
        return $this->repository->update($slider, $request);
    }

    public function remove($slider)
    {
        return $this->repository->remove($slider);
    }

}
