<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\AddProductToCartRequest;
use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiHelpers;

    private CartRepositoryInterface $cart;
    private ProductRepositoryInterface $product;

    public function __construct(
        CartRepositoryInterface $cart,
        ProductRepositoryInterface $product
    )
    {
        $this->cart = $cart;
        $this->product = $product;
    }

    /**
     * addinng product to cart
     */
    public function addProduct(AddProductToCartRequest $request): JsonResponse
    {
        $valid = $request->validated();
        $isAvailable = $this->product->checkStockIsAvailable($valid['product_id'], $valid['quantity']);
        if (!$isAvailable) {
            return $this->onError('Stock of product not available');
        }
        $cart = $this->cart->create([
            'product_id' => $valid['product_id'],
            'quantity' => $valid['quantity'],
            'user_id' => $request->user()->id
        ]);
        return $this->onSuccess($cart, 'Success add product to cart');
    }

    /**
     * remove product from cart
     */
    public function removeProduct(Request $request): JsonResponse
    {
        $valid = $request->validate(['cart_id' => 'required'], $request->all());
        $this->cart->deleteById($valid['cart_id']);
        return $this->onSuccess(null, 'Success remove product from cart');
    }

    /**
     * edit quantity of product
     */
    public function editQuantity(Request $request): JsonResponse
    {
        $valid = $request->validate(['cart_id' => 'required|integer', 'quantity' => 'required|integer'], $request->all());
        try {
            $cart = $this->cart->findById($valid['cart_id']);
        } catch (Exception $th) {
            return $this->onError('Cart not found');
        }
        $isAvailable = $this->product->checkStockIsAvailable($cart->product_id, $valid['quantity']);
        if (!$isAvailable) {
            return $this->onError('Stock of product not available');
        }
        $this->cart->update($valid['cart_id'], [
            'quantity' => $valid['quantity']
        ]);
        return $this->onSuccess(null, 'Success edit quantity');
    }
}
