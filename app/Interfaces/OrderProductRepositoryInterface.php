<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface OrderProductRepositoryInterface extends BaseRepositoryInterface
{
    public function createOrderProduct(array $payload);
}
