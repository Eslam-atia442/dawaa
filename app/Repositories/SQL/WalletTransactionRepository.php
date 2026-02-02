<?php

namespace App\Repositories\SQL;

use App\Models\WalletTransaction;
use App\Repositories\Contracts\WalletTransactionContract;

class WalletTransactionRepository extends BaseRepository implements WalletTransactionContract
{
    public function __construct(WalletTransaction $model)
    {
        parent::__construct($model);
    }
}
