<?php

namespace App\Repositories\Contracts;

interface SettingContract extends BaseContract
{
    public function updateSetting($request): bool;

}

