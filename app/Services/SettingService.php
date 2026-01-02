<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\SettingContract;
use Exception;
use Illuminate\Support\Facades\DB;

class SettingService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(SettingContract $repository)
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

    public function updateSettings($request)
    {
        return $this->repository->updateSetting($request);
    }

    public function remove($setting)
    {
        return $this->repository->remove($setting);
    }

}
