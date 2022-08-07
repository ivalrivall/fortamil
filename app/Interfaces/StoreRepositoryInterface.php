<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface StoreRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($payload);
}
