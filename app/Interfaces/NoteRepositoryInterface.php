<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;
use App\Models\Order;

interface NoteRepositoryInterface extends BaseRepositoryInterface
{
    public function saveOrderNote(Order $order, array $notePayload);
}
