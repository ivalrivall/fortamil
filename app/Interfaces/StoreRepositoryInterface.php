<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;
use App\Models\Store;

interface StoreRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($payload);
    /**
     * check store have on going order
     * @param Store $store
     */
    public function checkStoreHaveOnGoingOrder(Store $store);
}
