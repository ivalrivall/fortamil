<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

interface CartRepositoryInterface extends BaseRepositoryInterface
{
    public function editQuantity(int $cartId, int $quantity);
    public function addProduct(int $productId, int $qty, int $userId): ?Model;
    public function emptyCart(int $userId);
}
