<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface WarehouseRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($payload);
    public function getProductPaginate($payload, int $warehouseId);
    public function searchWarehouse($request);
    public function editService($request, $warehouseId);
    public function getWarehouseByProductList(array $productIds) : array;
}
