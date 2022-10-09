<?php

namespace App\Repositories;

use App\Http\Library\ApiHelpers;
use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\WarehouseRepositoryInterface;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CartRepository extends BaseRepository implements CartRepositoryInterface
{
    use ApiHelpers;
    /**
     * @var Model
     */
    protected $model;
    protected $product;
    protected $user;
    protected $warehouseRepo;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(
        Cart $model,
        ProductRepositoryInterface $product,
        UserRepositoryInterface $user,
        WarehouseRepositoryInterface $warehouseRepo
    )
    {
        $this->model = $model;
        $this->product = $product;
        $this->user = $user;
        $this->warehouseRepo = $warehouseRepo;
    }

    /**
     * edit quantity
     * @param int $cartId
     * @param int $quantity
     */
    public function editQuantity(int $cartId, int $quantity)
    {
        $cart = $this->findById($cartId);
        if (!$cart) {
            throw new Exception('Cart not found');
        }
        if ($quantity == 0) {
            return $this->deleteById($cartId);
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

        $carts = $this->model->select('product_id')->where('user_id', $userId)->get();
        if (count($carts) > 0) {
            $hasMulti = $this->hasMultiWarehouse(collect($carts)->pluck('product_id')->all());
            if ($hasMulti) {
                throw new Exception('Please pick product only in 1 warehouse');
            }
        }

        $cart = $this->hasSameProductOnUserCart($productId, $userId);
        if ($cart) {
            $quantity = $qty + $cart->quantity;
            $this->update($cart->id, [
                'quantity' => $quantity
            ]);
            $result = $this->findById($cart->id);
        } else {
            $result = $this->create([
                'product_id' => $productId,
                'quantity' => $qty,
                'user_id' => $userId
            ]);
        }
        return $result;
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

    /**
     * check product has been added to cart
     * @param int $productId
     * @param int $userId
     * @return Model
     */
    public function hasSameProductOnUserCart(int $productId, int $userId): ?Model
    {
        $cart = $this->model->where('product_id', $productId)->where('user_id', $userId)->first();
        return $cart;
    }

    /**
     * get cart by array id
     * @param array $cartId
     * @return Model
     */
    public function getCartByArrayId(array $cartId): ?Collection
    {
        $cart = $this->model->whereIn('id', $cartId)->get();
        return $cart;
    }

    /**
     * validate cart on other warehouse
     * @return bool
     */
    public function hasMultiWarehouse(array $productIds) : bool
    {
        $warehouseIds = $this->warehouseRepo->getWarehouseByProductList($productIds);
        if (count($warehouseIds) > 1) {
            return true;
        }
        return false;
    }
}
