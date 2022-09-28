<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface AddressRepositoryInterface extends BaseRepositoryInterface
{
    public function getAddressByUserService(int $userId);
}
