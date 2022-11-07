<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface InvoiceRepositoryInterface extends BaseRepositoryInterface
{
    public function makeInvoiceByOrder($payload);
}
