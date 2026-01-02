<?php

namespace App\Repositories\SQL;

use App\Models\ActivityLog;
use App\Repositories\Contracts\ActivityLogContract;
use Illuminate\Support\Facades\Storage;

class ActivityLogRepository extends BaseRepository implements ActivityLogContract
{
    /**
     * ActivityLogRepository constructor.
     * @param ActivityLog $model
     */
    public function __construct(ActivityLog $model)
    {
        parent::__construct($model);
    }
}
