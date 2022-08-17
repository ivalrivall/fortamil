<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getCartItems(int $userId);
}
