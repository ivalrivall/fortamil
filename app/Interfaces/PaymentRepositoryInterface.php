<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function payInvoiceOrder($payload);
}
