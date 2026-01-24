<?php

namespace App\Repositories\SQL;

use App\Models\Slider;
use App\Repositories\Contracts\SliderContract;

class SliderRepository extends BaseRepository implements SliderContract
{
    /**
     * SliderRepository constructor.
     * @param Slider $model
     */
    public function __construct(Slider $model)
    {
        parent::__construct($model);
    }
}
