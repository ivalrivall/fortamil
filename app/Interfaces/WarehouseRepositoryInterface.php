<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface WarehouseRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($payload);
}
