<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\ContactUsContract;
use Exception;
use Illuminate\Support\Facades\DB;

class ContactUsService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(ContactUsContract $repository)
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

    public function update($contactUs, $request)
    {
        return $this->repository->update($contactUs, $request);
    }

    public function remove($contactUs)
    {
        return $this->repository->remove($contactUs);
    }

}
