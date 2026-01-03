<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\CategoryContract;
use Exception;
use Illuminate\Support\Facades\DB;

class CategoryService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(CategoryContract $repository)
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

    public function update($category, $request)
    {
        return $this->repository->update($category, $request);
    }

    public function remove($category)
    {
        return $this->repository->remove($category);
    }

}
