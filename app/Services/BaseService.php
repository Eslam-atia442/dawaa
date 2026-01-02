<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use Exception;
use Illuminate\Support\Facades\DB;

class BaseService
{
    protected BaseContract $repository;

    public function __construct(BaseContract $repository)
    {
        $this->repository = $repository;
    }

    public function search($filters = [], $relations = [], $data = [])
    {
        return $this->repository->search($filters, $relations, $data);
    }

    public function fresh()
    {
        return $this->repository->freshRepo();
    }

    public function create($request)
    {
        DB::beginTransaction();
        $object = $this->repository->create($request);
        DB::commit();
        return $object;
    }

    public function update($modelObject, $request)
    {
        DB::beginTransaction();
        $object = $this->repository->update($modelObject, $request);
        DB::commit();
        return $object;
    }

    public function remove($modelObject)
    {
        return $this->repository->remove($modelObject);
    }

    public function toggleField($id, $field)
    {
        $modelObject = $this->repository->find($id);
        return $this->repository->toggleField($modelObject, $field);
    }

    public function find(int $id, array $relations = [], array $filters = [])
    {
        return $this->repository->find($id, $relations, $filters);
    }

    public function findBy(string $key, mixed $value)
    {

        return $this->repository->findBy($key, $value);
    }

    public function getByKey($column, $data): mixed
    {
        return $this->repository->getByKey($column, $data);
    }


}
