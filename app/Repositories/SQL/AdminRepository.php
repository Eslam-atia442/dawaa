<?php

namespace App\Repositories\SQL;

use App\Models\Admin;
use App\Repositories\Contracts\AdminContract;

class AdminRepository extends BaseRepository implements AdminContract
{
    /**
     * AdminRepository constructor.
     * @param Admin $model
     */
    public function __construct(Admin $model)
    {
        parent::__construct($model);
    }

    public function assignRole($admin, $role): mixed
    {
        return $admin->assignRole($role);
    }
}
