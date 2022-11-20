<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface ReturnRepositoryInterface extends BaseRepositoryInterface
{
    public function requestReturnService($payload);
}
