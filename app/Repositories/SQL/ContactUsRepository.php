<?php

namespace App\Repositories\SQL;

use App\Models\ContactUs;
use App\Repositories\Contracts\ContactUsContract;

class ContactUsRepository extends BaseRepository implements ContactUsContract
{
    /**
     * ContactUsRepository constructor.
     * @param ContactUs $model
     */
    public function __construct(ContactUs $model)
    {
        parent::__construct($model);
    }
}
