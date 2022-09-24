<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function addProduct(array $product): ?Model;
    public function checkStockIsAvailable(int $productId, int $qty): bool;
    public function disableProductService(array $data);
}
