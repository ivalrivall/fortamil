<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\Cart\AddProductToCartRequest;
use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        try {
            $cart = $this->cart->addProduct($valid['product_id'], $valid['quantity'], $request->user()->id);
        } catch (Exception $th) {
            return $this->onError($th->getMessage());
        }
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
            $this->cart->editQuantity($valid['cart_id'], $valid['quantity']);
        } catch (Exception $th) {
            return $this->onError($th->getMessage());
        }
        return $this->onSuccess(null, 'Success edit quantity');
    }

    public function emptyCart(Request $request): JsonResponse
    {
        try {
            $user = $this->cart->emptyCart($request->user()->id);
        } catch (Exception $th) {
            Log::error($th);
            return $this->onError($th->getMessage());
        }

        return $this->onSuccess(null, 'Success empty cart');
    }
}
