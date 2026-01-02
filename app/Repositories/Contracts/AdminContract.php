<?php

namespace App\Repositories\Contracts;

interface AdminContract extends BaseContract
{

    public function assignRole($admin, $role);

}

