<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface CustomerRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * create with address.
     */
    public function createWithAddress(array $customer, array $address);
    public function createWithAutoNameAndPhone(string $customerPlainAddress, $userId);
}
