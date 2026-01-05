<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\IntroContract;
use Exception;
use Illuminate\Support\Facades\DB;

class IntroService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(IntroContract $repository)
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

    public function update($intro, $request)
    {
        return $this->repository->update($intro, $request);
    }

    public function remove($intro)
    {
        return $this->repository->remove($intro);
    }

}
