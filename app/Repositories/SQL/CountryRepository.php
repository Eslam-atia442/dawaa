<?php

namespace App\Repositories\SQL;

use App\Models\Country;
use App\Repositories\Contracts\CountryContract;

class CountryRepository extends BaseRepository implements CountryContract
{
    /**
     * CountryRepository constructor.
     * @param Country $model
     */
    public function __construct(Country $model)
    {
        parent::__construct($model);
    }
}
