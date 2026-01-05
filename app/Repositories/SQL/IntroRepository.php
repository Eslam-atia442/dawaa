<?php

namespace App\Repositories\SQL;

use App\Models\Intro;
use App\Repositories\Contracts\IntroContract;

class IntroRepository extends BaseRepository implements IntroContract
{
    /**
     * IntroRepository constructor.
     * @param Intro $model
     */
    public function __construct(Intro $model)
    {
        parent::__construct($model);
    }
}
