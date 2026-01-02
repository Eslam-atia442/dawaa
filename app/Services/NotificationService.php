<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\SettingContract;
use Exception;
use Illuminate\Support\Facades\DB;

class NotificationService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(SettingContract $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    public function getNotifications($guard, $limit = null): array
    {
        $auth = auth()->guard($guard)->user();
        $notifications = $auth->notifications()->limit($limit)->latest()->get();
        $unReaded = $auth->unreadNotifications()->count();

        return [ 'notifications' => $notifications, 'unReaded' => $unReaded ];
    }
}
