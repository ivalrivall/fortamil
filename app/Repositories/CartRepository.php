<?php

namespace App\Repositories;

use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;

class CartRepository extends BaseRepository implements CartRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Cart $model)
    {
        $this->model = $model;
        $this->product = new ProductRepository(new Product());
        $this->user = new UserRepository(new User());
    }

    /**
     * edit quantity
     */
    public function editQuantity(int $cartId, int $quantity)
    {
        $cart = $this->findById($cartId);
        if (!$cart) {
            throw new Exception('Cart not found');
        }
        $isAvailable = $this->product->checkStockIsAvailable($cart->product_id, $quantity);
        if (!$isAvailable) {
            throw new Exception('Stock of product not available');
        }
        $cart = $this->update($cartId, [
            'quantity' => $quantity
        ]);
        return $cart;
    }

    /**
     * add product to cart
     */
    public function addProduct(int $productId, int $qty, int $userId): ?Model
    {
        $isAvailable = $this->product->checkStockIsAvailable($productId, $qty);
        if (!$isAvailable) {
            throw new Exception('Stock of product not available');
        }
        $cart = $this->create([
            'product_id' => $productId,
            'quantity' => $qty,
            'user_id' => $userId
        ]);
        return $cart;
    }

    /**
     * empty cart
     */
    public function emptyCart(int $userId)
    {
        $user = $this->user->findById($userId);
        if (!$user) {
            throw new Exception('User not found');
        }

        if (count($user->carts) == 0) {
            throw new Exception('Cart not found');
        }

        try {
            $cart = $user->carts()->delete();
        } catch (Exception $th) {
            throw new Exception('Failed empty cart');
        }

        return $cart;
    }
}
