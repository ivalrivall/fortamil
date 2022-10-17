<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function createOrder(array $payload);
    public function getUserOrder($payload);
    public function getDetailOrder($orderId);
}
